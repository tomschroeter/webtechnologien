function showToast(message, type = 'primary', autohide = true, delay = 5000) {
    const toastContainer = document.getElementById('globalToastContainer');
    
    if (!toastContainer) {
        console.error('Toast container not found! Make sure element with id "globalToastContainer" exists.');
        return;
    }
    
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap is not loaded! Make sure bootstrap.bundle.min.js is loaded before notification.js');
        return;
    }
    
    const toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);

    // Create toast HTML using bootstrap class
    const toastHTML = `
                <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="${autohide}" data-bs-delay="${delay}">
                    <div class="d-flex">
                        <div class="toast-body">
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;

    // Add toast to notification container
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);

    // Initialize and show toast
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement);
    toast.show();

    // Remove toast element from DOM after its hidden
    toastElement.addEventListener('hidden.bs.toast', function () {
        toastElement.remove();
    });

    return toast;
}

function showSuccessNotification(message) {
    return showToast(message, 'success');
}

function showErrorNotification(message) {
    return showToast(message, 'danger');
}

function showPrimaryNotification(message) {
    return showToast(message, 'primary');
}