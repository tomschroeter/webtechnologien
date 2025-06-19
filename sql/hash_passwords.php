<?php
/*
* This script is used to drop the 'Salt' column in the database,
* and to hash all passwords from default users provided by the Art_Database SQL script 
* To use this script, navigate to 'USER/xampp/htdocs/src/sql' and run 'C:\xampp\php\php.exe hash_passwords.php'
* MacOS: In src directory, you can run it with '/Applications/XAMPP/bin/php sql/hash_passwords.php '
*/

// Load env vars
require_once dirname(__DIR__) . "/env.php";

$host = $_ENV['DB_HOST'];
$db = $_ENV['DB_NAME'];
$user = $_ENV['DB_USERNAME'];
$pass = $_ENV['DB_PASSWORD'];
$charset = 'utf8mb4';

// DB connection setup
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $checkSalt = $pdo->query("SHOW COLUMNS FROM CustomerLogon LIKE 'Salt'");
    if ($checkSalt->rowCount() > 0) {
        $pdo->exec("ALTER TABLE CustomerLogon DROP COLUMN Salt");
        echo "Removed column 'Salt' succesfully.\n";
    } else {
        echo "Column 'Salt' doesn't exist.\n";
    }
    // Load all users
    $stmt = $pdo->query("SELECT CustomerID, Pass FROM CustomerLogon");
    $users = $stmt->fetchAll();

    // Prepare Update
    $updateStmt = $pdo->prepare("UPDATE CustomerLogon SET Pass = :hashed WHERE CustomerID = :id");

    foreach ($users as $user) {
        $originalPass = $user['Pass'];

        // Hash password if needed
        if (!password_get_info($originalPass)['algo']) {
            $hashed = password_hash($originalPass, PASSWORD_DEFAULT);

            $updateStmt->execute([
                ':hashed' => $hashed,
                ':id' => $user['CustomerID']
            ]);

            echo "Updated Customer ID {$user['CustomerID']}.\n";
        } else {
            echo "Customer ID {$user['CustomerID']} is already hashed.\n";
        }
    }

    echo "Process done.\n";

} catch (PDOException $e) {
    echo "Error when connecting to database: " . $e->getMessage();
}
