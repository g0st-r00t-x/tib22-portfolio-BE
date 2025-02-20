<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CleanExpiredQrCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-qr-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean cache files of expired qr codes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $keys = Cache::get('qr_limit_*');
        foreach ($keys as $key) {
            $data = Cache::get($key);
            if ($data['expires_at'] && Carbon::parse($data['expires_at'])->isPast()) {
                Cache::forget($key);
            }
        }
    }
}
