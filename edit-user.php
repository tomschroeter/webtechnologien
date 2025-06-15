<?php
// Redirect to new MVC route
$id = $_GET['id'] ?? null;
if ($id) {
    header("Location: /edit-profile/" . urlencode($id));
} else {
    header("Location: /edit-profile");
}
exit;
