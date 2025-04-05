<?php
require_once 'env.php';
loadEnv();

$host = $_ENV['DB_HOST'] ?? "localhost";
$user = $_ENV['DB_USERNAME'] ?? "root";
$pass = $_ENV['DB_PASSWORD'] ?? "";
$dbname = $_ENV['DB_NAME'] ?? "art";

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
