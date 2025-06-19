<!DOCTYPE html>
<html lang="en">

<?php require_once dirname(dirname(__DIR__)) . "/components/head.php"; ?>

<body>
    <?php require_once dirname(dirname(__DIR__)) . "/components/navbar.php"; ?>
    
    <div class="container">
        <!-- Flash Messages (converted to toast notifications) -->
        <?php 
        $flash = null;
        if (isset($flashMessage)) {
            $flash = $flashMessage;
        }
        ?>
        
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
    <!-- Debug: Check notifications -->
    <?php 
    if (isset($notifications)) {
        echo "<!-- Debug: notifications variable exists: " . json_encode($notifications) . " -->";
    } else {
        echo "<!-- Debug: notifications variable not set -->";
    }
    ?>
    
    <?php if (!empty($notifications)): ?>
        <script>
            console.log('Debug: About to show notifications:', <?= json_encode($notifications) ?>);
            document.addEventListener('DOMContentLoaded', function() {
                <?php foreach ($notifications as $notification): ?>
                    // Convert notification type to appropriate toast function
                    const notificationType = '<?= $notification['type'] ?>';
                    const notificationMessage = '<?= htmlspecialchars($notification['message']) ?>';
                    
                    console.log('Debug: Showing notification:', notificationType, notificationMessage);
                    
                    switch(notificationType) {
                        case 'success':
                            showSuccessNotification(notificationMessage);
                            break;
                        case 'danger':
                        case 'error':
                            showErrorNotification(notificationMessage);
                            break;
                        default:
                            showPrimaryNotification(notificationMessage);
                    }
                <?php endforeach; ?>
            });
        </script>
    <?php endif; ?>
    
    <!-- Legacy Flash Message Support -->
    <?php if ($flash): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const flashType = '<?= $flash['type'] ?>';
                const flashMessage = '<?= htmlspecialchars($flash['message']) ?>';
                
                switch(flashType) {
                    case 'success':
                        showSuccessNotification(flashMessage);
                        break;
                    case 'error':
                        showErrorNotification(flashMessage);
                        break;
                    default:
                        showPrimaryNotification(flashMessage);
                }
            });
        </script>
    <?php endif; ?>
</body>

</html>
