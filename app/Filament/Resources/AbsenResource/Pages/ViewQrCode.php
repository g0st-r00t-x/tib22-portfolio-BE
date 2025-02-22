<?php

namespace App\Filament\Resources\AbsenResource\Pages;

use App\Filament\Resources\AbsenResource;
use Filament\Resources\Pages\Page;

class ViewQrCode extends Page
{
    protected static string $resource = AbsenResource::class;

    protected static string $view = 'filament.resources.absen-resource.pages.view-qr-code';

    public $data;
    public $qrCode;
    public $expiresAt;
    public $canExtend;
    public $maxExtensions;
    public $extensionMinutes;

    public function mount()
    {
        $this->data = session('qr_data');

        // Mengisi variabel-variabel yang dibutuhkan view
        if ($this->data) {
            $this->qrCode = $this->data['qr_code'] ?? null;
            $this->expiresAt = $this->data['expires_at'] ?? null;
            $this->canExtend = $this->data['can_extend'] ?? false;
            $this->maxExtensions = $this->data['max_extensions'] ?? 0;
            $this->extensionMinutes = $this->data['extension_minutes'] ?? 0;
        }
    }
}
