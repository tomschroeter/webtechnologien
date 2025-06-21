<?php

/**
 * Loads environment variables from a .env file into the $_ENV superglobal.
 *
 * Each line in the .env file should be in KEY=VALUE format. Lines starting with "#" are ignored.
 * Existing keys in $_ENV are not overwritten.
 *
 * @param string $filePath The path to the .env file. Defaults to a file named ".env" in the same directory.
 * @throws Exception If the .env file is not found at the specified path.
 * @return void
 */
function loadEnv($filePath = __DIR__ . '/.env'): void
{
    if (!file_exists($filePath)) {
        throw new Exception('Environment file not found!');
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // Skip comment lines
        }

        // Split line into key and value
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        // Do not override existing $_ENV keys
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $value;
        }
    }
}

// Automatically load the .env file when the script is included
loadEnv();
