<!DOCTYPE html>
<html lang="en">

<?php require_once dirname(dirname(__DIR__)) . "/head.php"; ?>

<body class="container">
    <?php require_once dirname(dirname(__DIR__)) . "/navbar.php"; ?>
    
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
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
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
    <?php if (isset($_GET['welcome']) && isset($_SESSION['username'])): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <strong>Welcome to our Art Gallery, <?= htmlspecialchars($_SESSION['username']) ?>!</strong> 
            Thank you for registering. You can now explore our collection and add artworks to your favorites.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <!-- Main Content -->
    <?= $content ?>
    
    <?php require_once dirname(dirname(__DIR__)) . "/bootstrap.php"; ?>
</body>

</html>
