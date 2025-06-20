// Favorites AJAX functionality
document.addEventListener('DOMContentLoaded', function() {
    
    // Handle favorite button clicks
    document.addEventListener('click', async function(e) {
        const button = e.target.closest('.favorite-btn');

        if (!button) return;

        e.preventDefault();
        
        const type = button.dataset.type; // 'artist' or 'artwork'
        const id = button.dataset.id;
        
        // Disable button during request
        button.disabled = true;
        const originalText = button.textContent;
        button.textContent = 'Loading...';
        button.classList.add('loading');
        
        try {
            const endpoint = `/favorites/${type}s/${id}/toggle`;
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                const newIsFavorite = data.isFavorite;
                button.dataset.isFavorite = newIsFavorite ? 'true' : 'false';
                
                // Check if button originaly had only heart symbol (for cards) or full text
                const isHeartOnly = originalText.trim() === '♡' || originalText.trim() === '♥';
                
                if (newIsFavorite) {
                    button.className = 'btn favorite-btn btn-outline-danger';
                    button.innerHTML = isHeartOnly 
                        ? '<span class="heart">♥</span>' 
                        : '<span class="heart">♥</span> Remove from Favorites';

                    showSuccessNotification("Added to favorites!")
                } else {
                    button.className = 'btn favorite-btn btn-primary';
                    button.innerHTML = isHeartOnly 
                        ? '<span class="heart">♡</span>' 
                        : '<span class="heart">♡</span> Add to Favorites';

                    showSuccessNotification("Removed from favorites!")
                }
            } else {
                showErrorNotification(data.message);
                button.textContent = originalText;
            }
        } catch (error) {
            showErrorNotification('An error occurred while updating favorites');
            button.textContent = originalText;
        } finally {
            button.disabled = false;
            button.classList.remove('loading');
        }
    });
});