<x-filament-panels::page>
    <div class="relative w-full h-auto mx-auto bg-white rounded-xl shadow-lg">

        <div class="scanner-container">
            <!-- Video Element -->
            <video id="video" class="w-full h-full object-cover transform -scale-x-100"></video>

            <!-- Simplified Scanning Area Overlay -->
            <div class="scanning-overlay">
                <div class="scanning-area">
                    <div class="scanning-line"></div>
                    <!-- Corner markers -->
                    <div class="corner-marker top-left"></div>
                    <div class="corner-marker top-right"></div>
                    <div class="corner-marker bottom-left"></div>
                    <div class="corner-marker bottom-right"></div>
                </div>
            </div>


            {{-- PopUp Notification  --}}
            <div id="notification"
                class="fixed top-4 right-4 max-w-sm w-full bg-white rounded-lg shadow-lg transform transition-transform duration-300 translate-x-full z-50">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0" id="notification-icon">
                            <!-- Icon akan ditambahkan melalui JavaScript -->
                        </div>
                        <div class="ml-3 w-full">
                            <h3 id="notification-title" class="text-base font-medium text-gray-900"></h3>
                            <div id="notification-body" class="mt-1 text-sm text-gray-500"></div>
                        </div>
                        <button type="button" onclick="hideNotification()"
                            class="ml-4 text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            @php
                $userId = auth()->id();
                $csrfToken = csrf_token();
            @endphp

            <meta name="user-id" content="{{ $userId }}">
            <meta name="csrf-token" content="{{ $csrfToken }}">
        </div>

        @push('styles')
            <link rel='stylesheet' href='{{ asset('css/scan-qr.css') }}' />
        @endpush

        @push('scripts')
            <script type="text/javascript" src="https://unpkg.com/@zxing/library@latest"></script>
            <script src='{{ asset('js/scan-qr.js') }} '></script>
        @endpush
</x-filament-panels::page>
