// Constants
const NOTIFICATION_DURATION = 7000;
const NOTIFICATION_TYPES = {
    SUCCESS: "success",
    ERROR: "error",
    INFO: "info",
};

const NOTIFICATION_ICONS = {
    [NOTIFICATION_TYPES.SUCCESS]: `
    <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
  `,
    [NOTIFICATION_TYPES.ERROR]: `
    <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
    </svg>
  `,
    [NOTIFICATION_TYPES.INFO]: `
    <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
  `,
};

// Pure Functions for DOM manipulation
const getElement = (id) => document.getElementById(id);
const getMetaContent = (name) =>
    document.querySelector(`meta[name="${name}"]`)?.getAttribute("content");

// Pure function for notification element manipulation
const createNotificationState = () => ({
    notification: getElement("notification"),
    title: getElement("notification-title"),
    body: getElement("notification-body"),
    icon: getElement("notification-icon"),
});

// Pure function for showing notification
const showNotification =
    (state) =>
    (
        title,
        message,
        type = NOTIFICATION_TYPES.INFO,
        duration = NOTIFICATION_DURATION
    ) => {
        const { notification, title: titleEl, body, icon } = state;

        titleEl.textContent = title;
        body.textContent = message;

        notification.className =
            "fixed top-4 right-4 max-w-sm w-full bg-white rounded-lg shadow-lg z-50";
        notification.classList.add(`notification-${type}`);

        icon.innerHTML = NOTIFICATION_ICONS[type];
        notification.classList.add("slide-in");

        setTimeout(() => hideNotification(notification), duration);
    };

// Pure function for hiding notification
const hideNotification = (notification) => {
    if (notification) {
        notification.classList.remove("slide-in");
        notification.classList.add("slide-out");
        notification.style.display = "none";
    }
};

// Pure function for making HTTP requests
const makeRequest = (method, url, data = null) =>
    new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open(method, url, true);
        xhr.onload = () => resolve(xhr);
        xhr.onerror = () => reject(new Error("Network error"));
        xhr.send(data);
    });

// Pure function for parsing URL and extracting identifier
const getIdentifierFromUrl = (url) => {
    const urlObj = new URL(url);
    const pathParts = urlObj.pathname.split("/");
    return {
        identifier: pathParts[2],
        signature: urlObj.searchParams.get("signature"),
    };
};

// Pure function for creating form data
const createVerificationFormData = (identifier) => {
    const formData = new FormData();
    formData.append("identifier", identifier);
    formData.append("user_id", getMetaContent("user-id"));
    formData.append("_token", getMetaContent("csrf-token"));
    return formData;
};

// Pure function for handling scan result
const handleScanResult = (notify) => async (result) => {
    try {
        const { identifier, signature } = getIdentifierFromUrl(result.text);

        if (!identifier || !signature) {
            throw new Error("Invalid QR Code format");
        }

        notify(
            "Processing QR Code",
            "Sending data to server...",
            NOTIFICATION_TYPES.INFO,
            8000
        );

        const verifyResponse = await makeRequest(
            "GET",
            `/scan/${identifier}/verify?signature=${signature}`
        );

        if (verifyResponse.status === 200) {
            try{
                const formData = createVerificationFormData(identifier);
                const response = await makeRequest("POST", "/verify-qr", formData);
                const data = JSON.parse(response.responseText);
    
                notify(
                    data.status === "success" ? "Success!" : "Error!",
                    data.message,
                    data.status === "success"
                        ? NOTIFICATION_TYPES.SUCCESS
                        : NOTIFICATION_TYPES.ERROR
                );
            }catch (error){
                notify(
                    "Error",
                    "${error.message}",
                    NOTIFICATION_TYPES.ERROR
                );
            }
        } else {
            throw new Error("Invalid QR Code!");
        }
    } catch (error) {
        notify(
            "Error",
            error.message || "An error occurred",
            NOTIFICATION_TYPES.ERROR
        );
        console.error(error);
    }
};

// Pure function for handling errors
const handleError = (resultElement, debugElement) => (error) => {
    console.error(error);
    resultElement.textContent = error.message || "An error occurred";
    resultElement.className = "mt-2 text-center font-semibold text-red-500";
    if (debugElement) {
        debugElement.textContent = `Error: ${error.message}`;
    }
};

// Pure function for selecting preferred device
const getPreferredDevice = (devices) =>
    devices.find((device) => device.label.toLowerCase().includes("back"))
        ?.deviceId || devices[0].deviceId;

// Main scanner initialization function
const initializeScanner = async () => {
    const codeReader = new ZXing.BrowserQRCodeReader();
    const resultElement = getElement("result");
    const debugElement = getElement("debug");
    const notificationState = createNotificationState();
    const notify = showNotification(notificationState);

    try {
        const devices = await codeReader.getVideoInputDevices();
        const selectedDeviceId = getPreferredDevice(devices);

        // Start continuous scanning
        codeReader.decodeFromInputVideoDeviceContinuously(
            selectedDeviceId,
            "video",
            (result, error) => {
                if (result) {
                    handleScanResult(notify)(result);
                }
                if (error && !(error instanceof ZXing.NotFoundException)) {
                    handleError(resultElement, debugElement)(error);
                }
            }
        );
    } catch (error) {
        handleError(resultElement, debugElement)(error);
    }
};

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", initializeScanner);
