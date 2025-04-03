<?php
function route($name, $params = [])
{
  $routes = require "routes.php"; // Load routes

  if (!isset($routes[$name])) {
    throw new Exception("Route '$name' not found.");
  }

  $path = $routes[$name]; // Get the route pattern

  // Replace placeholders with actual values
  foreach ($params as $key => $value) {
    $path = str_replace(":$key", $value, $path);
  }

  // Replace required keys
  foreach ($params as $key => $value) {
    $path = str_replace("{$key}", $value, $path);
  }

  // Remove any leftover optional placeholders (e.g., ":id" if not replaced)
  $path = preg_replace('/\/?:\w+/', '', $path);

  return trim($path, "/"); // Ensure no double slashes
}
?>