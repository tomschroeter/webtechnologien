<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="error-template">
                <h1 class="display-1 text-muted">500</h1>
                <h2 class="display-4">Server Error</h2>
                <div class="error-details mt-4 mb-4">
                    <?php if (isset($_SESSION['error_message'])): ?>
                        <p class="lead"><?= htmlspecialchars($_SESSION['error_message']) ?></p>
                        <?php unset($_SESSION['error_message']); ?>
                    <?php else: ?>
                        <p class="lead">Something went wrong on our end.</p>
                        <p class="text-muted">We're working to fix the issue. Please try again later.</p>
                    <?php endif; ?>
                </div>
                <div class="error-actions">
                    <a href="/" class="btn btn-primary btn-lg mr-2">
                        <i class="fa fa-home"></i> Go Home
                    </a>
                    <a href="javascript:history.back()" class="btn btn-outline-primary btn-lg">
                        Go Back
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
