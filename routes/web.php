<?php

use App\Http\Controllers\GenerateQrCode;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::post('/verify-qr', [GenerateQrCode::class, 'verifyQr'])->name('verify.qr');

// Route::post('/scan/{identifier}', [GenerateQrCode::class, 'scan'])
//   ->name('scan-qr-code');

Route::get('/scan/{identifier}/verify', [GenerateQrCode::class, 'verify'])
  ->name('verify-qr-code')
  ->middleware('signed');

require __DIR__.'/auth.php';
