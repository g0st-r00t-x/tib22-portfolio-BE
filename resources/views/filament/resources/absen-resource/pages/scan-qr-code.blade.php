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
        </div <!-- Result & Debug Container -->
        <div id="result" class="mt-4 text-center font-semibold text-green-500 transition-all duration-300"></div>
        <div id="debug" class="mt-2 text-xs text-gray-500 break-all"></div>
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

    <style>
        /* PopUp Notification */
        .notification-success {
            border-left: 4px solid #10B981;
        }

        .notification-error {
            border-left: 4px solid #EF4444;
        }

        .notification-info {
            border-left: 4px solid #3B82F6;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
            }

            to {
                transform: translateX(0);
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(100%);
            }
        }

        .slide-in {
            animation: slideIn 0.3s forwards;
        }

        .slide-out {
            animation: slideOut 0.3s forwards;
        }

        /* Scanner Container */
        .scanner-container {
            position: relative;
            width: 100%;
            height: 100vh;
            max-height: 70vh;
            margin: 0 auto;
            overflow: hidden;
            border-radius: 8px;
            background: #000;
        }

        /* Responsive adjustments */
        @media (min-width: 1024px) {

            /* Desktop */
            .scanner-container {
                max-height: 90vh;
                height: 90vh;
            }
        }

        @media (max-width: 768px) {

            /* Mobile */
            .scanner-container {
                height: 70vh;
            }
        }

        #video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .scanning-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .scanning-area {
            position: relative;
            width: min(300px, 80vw);
            height: min(300px, 80vw);
            border: 2px solid #ffffff;
            overflow: hidden;
            background: transparent;
            flex-shrink: 0;
        }

        @media (max-width: 768px) {
            .scanning-area {
                width: min(250px, 80vw);
                height: min(250px, 80vw);
            }
        }

        @media (max-width: 480px) {
            .scanning-area {
                width: min(200px, 80vw);
                height: min(200px, 80vw);
            }
        }

        .scanning-line {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg,
                    transparent 0%,
                    #ffffff 50%,
                    transparent 100%);
            box-shadow: 0 0 8px #ffffff;
            animation: scan 2s linear infinite;
        }

        /* Corner markers */
        .corner-marker {
            position: absolute;
            width: 20px;
            height: 20px;
            border-color: #ffffff;
            border-style: solid;
        }

        .top-left {
            top: 0;
            left: 0;
            border-width: 3px 0 0 3px;
        }

        .top-right {
            top: 0;
            right: 0;
            border-width: 3px 3px 0 0;
        }

        .bottom-left {
            bottom: 0;
            left: 0;
            border-width: 0 0 3px 3px;
        }

        .bottom-right {
            bottom: 0;
            right: 0;
            border-width: 0 3px 3px 0;
        }

        @keyframes scan {
            from {
                top: 0;
            }

            to {
                top: 100%;
            }
        }
    </style>

    <script type="text/javascript" src="https://unpkg.com/@zxing/library@latest"></script>
    <script>
        function showNotification(title, message, type = 'info', duration = 3000) {
            const notification = document.getElementById('notification');
            const notificationTitle = document.getElementById('notification-title');
            const notificationBody = document.getElementById('notification-body');
            const notificationIcon = document.getElementById('notification-icon');

            // Set content
            notificationTitle.textContent = title;
            notificationBody.textContent = message;

            // Reset classes
            notification.className = 'fixed top-4 right-4 max-w-sm w-full bg-white rounded-lg shadow-lg z-50';

            // Add type-specific styles
            let iconSvg = '';
            switch (type) {
                case 'success':
                    notification.classList.add('notification-success');
                    iconSvg = `<svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>`;
                    break;
                case 'error':
                    notification.classList.add('notification-error');
                    iconSvg = `<svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>`;
                    break;
                default:
                    notification.classList.add('notification-info');
                    iconSvg = `<svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>`;
            }

            notificationIcon.innerHTML = iconSvg;

            // Show notification
            notification.classList.add('slide-in');

            // Hide after duration
            setTimeout(hideNotification, duration);
        }

        function hideNotification() {
            const notification = document.getElementById('notification');
            if (notification) {
                // Hapus class 'slide-in' dan tambahkan class 'slide-out'
                notification.classList.remove('slide-in');
                notification.classList.add('slide-out');

                notification.style.display = 'none';
            };
        }


        document.addEventListener('DOMContentLoaded', function() {
            const codeReader = new ZXing.BrowserQRCodeReader();
            const resultElement = document.getElementById('result');
            const debugElement = document.getElementById('debug');

            // Modify handleScan function to use the new notification
            function handleScan(qrData) {
                const formData = new FormData();
                formData.append('identifier', qrData.identifier);
                formData.append('text', qrData.text);
                formData.append('signature', qrData.signature);
                formData.append('user_id', document.querySelector('meta[name="user-id"]').getAttribute('content'));
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute(
                    'content'));

                // Show debug info in notification
                showNotification(
                    'Processing QR Code',
                    'Sending data to server...',
                    'info',
                    8000
                );

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/verify-qr', true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            showNotification(
                                response.status === 'success' ? 'Success!' : 'Error!',
                                response.message,
                                response.status === 'success' ? 'success' : 'error',
                                3000
                            );
                        } catch (e) {
                            showNotification(
                                'Error',
                                'Failed to process server response',
                                'error',
                                3000
                            );
                        }
                    }
                };
                xhr.send(formData);
            }

            codeReader.getVideoInputDevices()
                .then(videoInputDevices => {
                    const selectedDeviceId = videoInputDevices.find(device =>
                            device.label.toLowerCase().includes('back'))?.deviceId ||
                        videoInputDevices[0].deviceId;

                    codeReader.decodeFromInputVideoDeviceContinuously(
                        selectedDeviceId,
                        'video',
                        (result, err) => {
                            if (result) {
                                try {
                                    const qrData = JSON.parse(result.text);
                                    handleScan(qrData);
                                } catch (error) {
                                    resultElement.textContent = 'Invalid QR Code format';
                                    resultElement.className = 'mt-2 text-center font-semibold text-red-500';
                                    debugElement.textContent = 'Error: ' + error.message + '\nData: ' +
                                        result.text;
                                }
                            }
                            if (err && !(err instanceof ZXing.NotFoundException)) {
                                console.error(err);
                            }
                        }
                    );
                })
                .catch(err => {
                    console.error(err);
                    resultElement.textContent = 'Error accessing camera';
                    resultElement.className = 'mt-2 text-center font-semibold text-red-500';
                });
        });
    </script>
</x-filament-panels::page>
