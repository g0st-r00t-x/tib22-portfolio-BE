<?php

namespace App\Filament\Resources\AbsenResource\Pages;

use App\Filament\Resources\AbsenResource;
use Filament\Resources\Pages\Page;

class ViewQrCode extends Page
{
    protected static string $resource = AbsenResource::class;

    protected static string $view = 'filament.resources.absen-resource.pages.view-qr-code';

    public $qrCode;
    public $expiresAt;
    public $canExtend;
    public $maxExtensions;
    public $extensionMinutes;

    public function mount()
    {
        // Mengambil data dari parameter URL
        $this->qrCode = request('qrCode');
        $this->expiresAt = request('expiresAt');
        $this->canExtend = request('canExtend');
        $this->maxExtensions = request('maxExtensions');
        $this->extensionMinutes = request('extensionMinutes');
    }
}
