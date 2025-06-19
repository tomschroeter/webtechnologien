<!DOCTYPE html>
<html lang="en">

<?php require_once dirname(dirname(__DIR__)) . "/components/head.php"; ?>

<body>
    <?php require_once dirname(dirname(__DIR__)) . "/components/navbar.php"; ?>
    
    <div class="container">
        <!-- Flash Messages -->
        <?php 
        $flash = null;
        if (isset($flashMessage)) {
            $flash = $flashMessage;
        }
        ?>
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show mt-3" role="alert">
                <?= htmlspecialchars($flash['message']) ?>
                <button type="button" class="close" onclick="this.parentElement.style.display='none'" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        
        <!-- Login success message -->
        <?php if (isset($_GET['login']) && $_GET['login'] === 'success' && isset($_SESSION['username'])): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <strong>Welcome back, <?= htmlspecialchars($_SESSION['username']) ?>!</strong> 
            You have successfully logged in.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Welcome message for newly registered users -->
    <?php if (isset($_GET['welcome']) && $_GET['welcome'] === '1' && isset($_SESSION['username'])): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <strong>Welcome to Art Gallery, <?= htmlspecialchars($_SESSION['username']) ?>!</strong> 
            Your account has been created successfully.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>        <?php endif; ?>
        
        <!-- URL Message Parameters -->
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <?= htmlspecialchars($_GET['message']) ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        
        <!-- Main Content -->
        <?= $content ?>
        
    </div> <!-- End container -->
    
    <!-- Include favorites JavaScript to stay on same page when adding or removing favorite -->
    <script src="/assets/js/favorites.js"></script>

    <!-- Include Bootstrap JS for Dropdowns, Accordions and Closing Banners -->
    <script src="/assets/bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
    
</body>

</html>
