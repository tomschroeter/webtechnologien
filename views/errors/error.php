<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="error-template py-5">
                <h1 class="display-1 text-muted fw-bold"><?= $statusCode ?></h1>
                <h2 class="display-4 mb-3"><?= htmlspecialchars($statusText) ?></h2>
                <div class="error-details mb-4">
                    <p class="lead"><?= htmlspecialchars($message) ?></p>
                    <?php if ($statusCode >= 500): ?>
                        <p class="text-muted">We're working to fix the issue. Please try again later.</p>
                    <?php elseif ($statusCode === 404): ?>
                        <p class="text-muted">The page may have been moved, deleted, or you entered the wrong URL.</p>
                    <?php endif; ?>
                </div>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="/" class="btn btn-primary btn-lg">
                        <i class="fa fa-home me-1"></i> Go Home
                    </a>
                    <?php if ($statusCode >= 500): ?>
                        <a href="javascript:history.back()" class="btn btn-outline-primary btn-lg">
                            Go Back
                        </a>
                    <?php else: ?>
                        <a href="/artists" class="btn btn-outline-primary btn-lg">
                            Browse Artists
                        </a>
                        <a href="/artworks" class="btn btn-outline-primary btn-lg">
                            Browse Artworks
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.error-template h1 {
    font-size: 8rem;
}

@media (max-width: 768px) {
    .error-template h1 {
        font-size: 4rem;
    }
    .error-template h2 {
        font-size: 1.5rem;
    }
}
</style>
