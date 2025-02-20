<?php

namespace App\Filament\Resources\AbsenResource\Pages;

use App\Filament\Resources\AbsenResource;
use Filament\Resources\Pages\Page;

class ScanQrCode extends Page
{
    protected static string $resource = AbsenResource::class;

    protected static string $view = 'filament.resources.absen-resource.pages.scan-qr-code';
}
