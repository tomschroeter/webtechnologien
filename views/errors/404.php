<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="error-template">
                <h1 class="display-1 text-muted">404</h1>
                <h2 class="display-4">Page Not Found</h2>
                <div class="error-details mt-4 mb-4">
                    <?php if (isset($_SESSION['error_message'])): ?>
                        <p class="lead"><?= htmlspecialchars($_SESSION['error_message']) ?></p>
                        <?php unset($_SESSION['error_message']); ?>
                    <?php else: ?>
                        <p class="lead">Sorry, the page you are looking for doesn't exist.</p>
                        <p class="text-muted">The page may have been moved, deleted, or you entered the wrong URL.</p>
                    <?php endif; ?>
                </div>
                <div class="error-actions">
                    <a href="/" class="btn btn-primary btn-lg mr-2">
                        <i class="fa fa-home"></i> Go Home
                    </a>
                    <a href="/artists" class="btn btn-outline-primary btn-lg mr-2">
                        Browse Artists
                    </a>
                    <a href="/artworks" class="btn btn-outline-primary btn-lg">
                        Browse Artworks
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.error-template {
    padding: 40px 15px;
}
.error-template h1 {
    font-size: 8rem;
    font-weight: 700;
}
</style>
