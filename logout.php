<?php
// Try MVC routing first
require_once "mvc_bootstrap.php";

// If we reach here, MVC didn't handle the route, continue with original logic
session_start();
session_destroy();
header("Location: login.php?logout=1");
exit;
