<x-filament::page>
    <div class="p-6 bg-slate-900 dark:bg-gray-200 rounded-lg shadow">
        @if ($qrCode)
            <div class="flex flex-col items-center space-y-4">
                <div class="text-lg font-medium">QR Code</div>

                <div class="">
                    <img class="bg-white rounded-lg p-8" id="qrCodeImage" src="data:image/png;base64,{{ $qrCode }}" alt="QR Code">
                </div>

                <!-- Tombol Download -->
                <button id="downloadButton" class="px-4 py-2 bg-blue-500 text-black rounded hover:bg-blue-600">
                    Download QR Code
                </button>

                @if ($expiresAt)
                    <div class="text-sm bg-red-500/20 text-black">
                        Expires at: {{ \Carbon\Carbon::parse($expiresAt)->format('Y-m-d H:i:s') }}
                    </div>
                @endif
                @if ($canExtend)
                    <div class="text-sm bg-indigo-900 text-black">
                        Extensions: {{ $maxExtensions == 0 ? 'Unlimited' : $maxExtensions }}
                        ({{ $extensionMinutes }} minutes each)
                    </div>
                @endif
            </div>
        @else
            <div class="text-center text-black">
                No QR code available
            </div>
        @endif
    </div>

    <!-- JavaScript untuk Mengunduh Gambar -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const downloadButton = document.getElementById('downloadButton');
            const qrCodeImage = document.getElementById('qrCodeImage');

            if (downloadButton && qrCodeImage) {
                downloadButton.addEventListener('click', function () {
                    // Buat elemen <a> untuk mengunduh gambar
                    const link = document.createElement('a');
                    link.href = qrCodeImage.src; // Ambil sumber gambar dari elemen <img>
                    link.download = 'qrcode.png'; // Nama file yang akan diunduh
                    document.body.appendChild(link); // Tambahkan elemen <a> ke DOM
                    link.click(); // Klik elemen <a> untuk memulai unduhan
                    document.body.removeChild(link); // Hapus elemen <a> setelah digunakan
                });
            }
        });
    </script>
</x-filament::page>