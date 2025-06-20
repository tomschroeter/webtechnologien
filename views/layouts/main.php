<!DOCTYPE html>
<html lang="en">

<?php require_once dirname(dirname(__DIR__)) . "/components/head.php"; ?>

<body>
    <?php require_once dirname(dirname(__DIR__)) . "/components/navbar.php"; ?>
    
    <div class="container">
        
        <!-- Main Content -->
        <?= $content ?>
        
    </div> <!-- End container -->
    
    <!-- Global Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="globalToastContainer">
        <!-- Toasts will be added here by notification.js -->
    </div>
    

    <!-- Include Bootstrap JS for Dropdowns, Accordions and Closing Banners -->
    <script src="/assets/bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Include our own scripts for notifications and adding favorites in the background -->
    <script src="/assets/js/favorites.js"></script>
    <script src="/assets/js/notification.js"></script>
    
    <!-- Notifications Toast Handler -->
    <?php if (!empty($notifications)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const notifications = <?= json_encode($notifications) ?>;
                
                notifications.forEach(function(notification) {
                    switch(notification.type) {
                        case 'success':
                            showSuccessNotification(notification.message);
                            break;
                        case 'error':
                            showErrorNotification(notification.message);
                            break;
                        default:
                            showSuccessNotification(notification.message);
                    }
                });
            });
        </script>
    <?php endif; ?>
</body>

<?php require_once dirname(dirname(__DIR__)) . "/components/footer.php"; ?>

</html>
