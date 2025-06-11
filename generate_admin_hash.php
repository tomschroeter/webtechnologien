<?php
// Generate correct hash for AdminPassword123!
$password = 'AdminPassword123!';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: " . $password . PHP_EOL;
echo "Generated Hash: " . $hash . PHP_EOL;

// Extract salt for database
function extractSaltFromHash(string $hash): string {
    if (preg_match('/^\$2[axy]\$\d+\$(.{22})/', $hash, $matches)) {
        return $matches[1];
    }
    return '';
}

$salt = extractSaltFromHash($hash);
echo "Extracted Salt: " . $salt . PHP_EOL;

// Verify it works
echo "Verification: " . (password_verify($password, $hash) ? "PASS" : "FAIL") . PHP_EOL;

// Test against the current wrong hash
$currentHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
echo "Current hash verification: " . (password_verify($password, $currentHash) ? "PASS" : "FAIL") . PHP_EOL;
?>
