// Function to display a small notification popup in the corner
function showNotification(message, type = 'info') {
    // Create or get notification container
    let container = document.getElementById('notification-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notification-container';
        container.style.cssText = 'position: fixed; top: 20px; right: 20px; width: 350px; z-index: 9999;';
        document.body.appendChild(container);
    }
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.style.cssText = 'margin-bottom: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);';
    alert.innerHTML = `
        ${message}
        <button type="button" class="close" onclick="this.parentElement.remove()" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    container.appendChild(alert);
    
    // Auto-dismiss after 4 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 4000);
}