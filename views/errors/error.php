<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="error-template py-5">
                <!-- Display status code (e.g., 404, 500) -->
                <h1 class="display-1 text-muted fw-bold"><?= $statusCode ?></h1>
                <!-- Display status text (e.g., Not Found, Internal Server Error) -->
                <h2 class="display-4 mb-3"><?= htmlspecialchars($statusText) ?></h2>
                <div class="error-details mb-4">
                    <!-- Display detailed error message -->
                    <p class="lead"><?= htmlspecialchars($message) ?></p>
                    <!-- Show additional info based on error type -->
                    <?php if ($statusCode >= 500): ?>
                        <p class="text-muted">We're working to fix the issue. Please try again later.</p>
                    <?php elseif ($statusCode === 404): ?>
                        <p class="text-muted">The page may have been moved, deleted, or you entered the wrong URL.</p>
                    <?php endif; ?>
                </div>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <!-- Link to homepage -->
                    <a href="/" class="btn btn-primary btn-lg">
                        <i class="fa fa-home me-1"></i> Go Home
                    </a>
                    <!-- If server error, offer "Go Back" button -->
                    <?php if ($statusCode >= 500): ?>
                        <a href="javascript:history.back()" class="btn btn-outline-primary btn-lg">
                            Go Back
                        </a>
                        <!-- Otherwise, provide browsing options -->
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