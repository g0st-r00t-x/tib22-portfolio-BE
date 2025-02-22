<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GenerateQrCode extends Controller
{
    public function generate(Request $request, $text)
    {
        // Validate time limitation settings
        $validated = $request->validate([
            'has_time_limit' => 'required|boolean',
            'time_limit_minutes' => 'required_if:has_time_limit,true|integer|min:1',
            'can_extend' => 'required|boolean',
            'max_extensions' => 'required_if:can_extend,true|integer|min:0',
            'extension_minutes' => 'required_if:can_extend,true|integer|min:1',
        ]);

        // Convert values to integers and set defaults if not applicable
        $timeLimitMinutes = intval($validated['time_limit_minutes']) ?? 0;
        $maxExtensions = $validated['can_extend'] ? intval($validated['max_extensions']) : 0;
        $extensionMinutes = $validated['can_extend'] ? intval($validated['extension_minutes']) : 0;

        // Generate unique identifier
        $identifier = Str::random(32);
        $signature = hash_hmac('sha256', $identifier, config('app.key'));

        // Store time limitation data
        $limitData = [
            'text' => $text,
            'created_at' => now(),
            'expires_at' => $validated['has_time_limit']
            ? now()->addMinutes($timeLimitMinutes)
            : null,
            'extension_count' => 0,
            'can_extend' => $validated['can_extend'],
            'max_extensions' => $validated['can_extend'] ? $maxExtensions : 0,
            'extension_minutes' => $validated['can_extend'] ? $extensionMinutes : 0,
            'scanned_users' => [], // Tambahkan array untuk menyimpan pengguna yang sudah memindai
        ];

        // Store limitation data in cache
        Cache::put("qr_limit_{$identifier}", $limitData, $validated['has_time_limit']
        ? Carbon::parse($limitData['expires_at'])->addDay()
            : now()->addYear());

        $signedUrl = URL::signedRoute('verify-qr-code', ['identifier' => $identifier]);

        // Generate QR code with the actual text content
        $qrCode = QrCode::format('png')
            ->size(300)
            ->color(0, 0, 0)
            ->backgroundColor(255, 255, 255)
            ->margin(4)
            ->generate($signedUrl);
        return response([
            'qr_code' => base64_encode($qrCode),
            'text' => $text,
            'expires_at' => $limitData['expires_at'],
            'can_extend' => $limitData['can_extend'],
            'max_extensions' => $limitData['max_extensions'],
            'extension_minutes' => $limitData['extension_minutes'],
        ]);
        // return response([
        //     'identifier' => $identifier,
        // ]);
    }

    public function verifyQr(Request $request)
    {
        try {
            // Log incoming request
            Log::info('Incoming QR verification request:', $request->all());

            // Validation
            try {
                $validated = $request->validate([
                    'identifier' => 'required|string',
                    'user_id' => 'required', // Removed string validation
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::error('Validation failed:', $e->errors());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }

            $identifier = $validated['identifier'];
            $userId = $validated['user_id'];

            // Log validated data
            Log::info('Validated data:', $validated);

            // Cache check
            $limitData = Cache::get("qr_limit_{$identifier}");
            Log::info('Cache data for ' . $identifier . ':', ['data' => $limitData]);

            if (!$limitData) {
                Log::warning('No cache data found for identifier: ' . $identifier);
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR code not found or expired'
                ], 404);
            }

            // Expiration check
            if ($limitData['expires_at'] && now()->isAfter($limitData['expires_at'])) {
                Log::info('QR code expired:', [
                    'expires_at' => $limitData['expires_at'],
                    'current_time' => now()
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR code has expired'
                ], 410);
            }

            // Previous scan check
            if (in_array($userId, $limitData['scanned_users'])) {
                Log::info('User already scanned:', [
                    'user_id' => $userId,
                    'identifier' => $identifier
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'You have already scanned this QR code'
                ], 403);
            }

            // Update scanned users
            $limitData['scanned_users'][] = $userId;
            $expiryTime = Carbon::parse($limitData['expires_at'])->addDay();

            Cache::put("qr_limit_{$identifier}", $limitData, $expiryTime);

            Log::info('Successfully processed QR code:', [
                'identifier' => $identifier,
                'user_id' => $userId
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'QR code is valid',
                'text' => $limitData['text'],
            ]);
        } catch (\Exception $e) {
            Log::error('QR verification error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing the QR code',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function verify($identifier)
    {
        Log::info('Identifier', ['identifier' => $identifier]);

        // Karena kita menggunakan middleware 'signed',
        // jika request sampai ke sini berarti signature sudah valid
        return response()->json([
            'status' => 'success',
            'message' => 'Signature valid',
            'identifier' => $identifier
        ]);
    }

    public function scan(Request $request, $identifier)
    {
        

        // Periksa apakah data ada di cache
        if (!Cache::has("qr_limit_{$identifier}")) {
            Log::info('Data QR code tidak ada di cache.');
            return response()->json(['message' => 'QR Code tidak valid atau sudah kedaluwarsa.'], 404);
        }

        $data = Cache::get("qr_limit_{$identifier}");
        Log::info('Data pada cache', $data);

        return response()->json([
            'message' => 'QR Code valid',
            'data' => $data,
        ]);
    }


    public function extend($identifier)
    {
        $limitData = Cache::get("qr_limit_{$identifier}");
        if (!$limitData) {
            return response()->json([
                'status' => 'error',
                'message' => 'QR code not found'
            ], 404);
        }

        if (!$limitData['can_extend']) {
            return response()->json([
                'status' => 'error',
                'message' => 'This QR code cannot be extended'
            ], 403);
        }

        // Ensure all required keys exist with default values
        $limitData['extension_count'] = $limitData['extension_count'] ?? 0;
        $limitData['extension_minutes'] = $limitData['extension_minutes'] ?? 0;
        $limitData['max_extensions'] = $limitData['max_extensions'] ?? 0;

        // Convert values to integers
        $limitData['extension_count'] = intval($limitData['extension_count']);
        $limitData['extension_minutes'] = intval($limitData['extension_minutes']);
        $limitData['max_extensions'] = intval($limitData['max_extensions']);

        if ($limitData['max_extensions'] !== 0 && $limitData['extension_count'] >= $limitData['max_extensions']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Maximum extensions reached'
            ], 403);
        }

        // Calculate new expiration
        $newExpiresAt = $limitData['expires_at']
        ? Carbon::parse($limitData['expires_at'])->addMinutes($limitData['extension_minutes'])
        : now()->addMinutes($limitData['extension_minutes']);

        // Update data
        $limitData['expires_at'] = $newExpiresAt;
        $limitData['extension_count']++;

        // Store updated data
        Cache::put("qr_limit_{$identifier}", $limitData, $newExpiresAt->addDay());

        return response()->json([
            'status' => 'success',
            'new_expires_at' => $newExpiresAt,
            'extensions_remaining' => $limitData['max_extensions'] === 0 ? 'unlimited' :
            $limitData['max_extensions'] - $limitData['extension_count']
        ]);
    }
}
