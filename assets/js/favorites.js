// Favorites AJAX functionality
document.addEventListener('DOMContentLoaded', function() {
    // Notification function
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
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
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
    
    // Handle favorite button clicks
    document.addEventListener('click', async function(e) {
        if (e.target.classList.contains('favorite-btn')) {
            e.preventDefault();
            
            const button = e.target;
            const type = button.dataset.type; // 'artist' or 'artwork'
            const id = button.dataset.id;
            const isFavorite = button.dataset.isFavorite === 'true';
            
            // Disable button during request
            button.disabled = true;
            const originalText = button.textContent;
            button.textContent = 'Loading...';
            button.classList.add('loading');
            
            try {
                const endpoint = `/api/favorites/${type}s/${id}/toggle`;
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Update button state
                    const newIsFavorite = data.isFavorite;
                    button.dataset.isFavorite = newIsFavorite ? 'true' : 'false';
                    
                    // Check if button originally had only heart symbol (for cards) or full text
                    const isHeartOnly = originalText.trim() === '♡' || originalText.trim() === '♥';
                    
                    if (newIsFavorite) {
                        button.className = 'btn favorite-btn btn-outline-danger';
                        button.textContent = isHeartOnly ? '♥' : '♥ Remove from Favorites';
                    } else {
                        button.className = 'btn favorite-btn btn-primary';
                        button.textContent = isHeartOnly ? '♡' : '♡ Add to Favorites';
                    }
                    
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message, 'danger');
                    button.textContent = originalText;
                }
            } catch (error) {
                showNotification('An error occurred while updating favorites', 'danger');
                button.textContent = originalText;
            } finally {
                button.disabled = false;
                button.classList.remove('loading');
            }
        }
    });
});