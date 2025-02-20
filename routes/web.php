<?php

use App\Http\Controllers\GenerateQrCode;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::post('/verify-qr', [GenerateQrCode::class, 'verifyQr'])->name('verify.qr');


require __DIR__.'/auth.php';
