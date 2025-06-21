<?php

require_once __DIR__ . "/env.php";

/**
 * Class Database
 *
 * Handles connection to a MySQL database using PDO.
 * Provides basic methods for connection management, query preparation,
 * and transaction handling.
 */
class Database
{
    private ?PDO $pdo;

    /**
     * Establishes a new database connection using environment variables.
     *
     * @throws Exception If already connected or connection fails.
     * @return void
     */
    public function connect(): void
    {
        if ($this->isConnected()) {
            throw new Exception("Already connected to DB.");
        }

        try {
            $host = $_ENV['DB_HOST'];
            $user = $_ENV['DB_USERNAME'];
            $pass = $_ENV['DB_PASSWORD'];
            $dbname = $_ENV['DB_NAME'];

            $connectionString = "mysql:host=" . $host . ";dbname=" . $dbname;

            $this->pdo = new PDO($connectionString, $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            exit("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Prepares a SQL statement using the active database connection.
     *
     * @param string $sql The SQL query to prepare.
     * @return PDOStatement The prepared statement object.
     * @throws Exception If not connected to the database.
     */
    public function prepareStatement(string $sql): PDOStatement
    {
        if (!$this->isConnected()) {
            throw new Exception("Not connected to DB.");
        }

        return $this->pdo->prepare($sql);
    }

    /**
     * Checks if the database is currently connected.
     *
     * @return bool True if connected; false otherwise.
     */
    public function isConnected(): bool
    {
        return $this->pdo !== null;
    }

    /**
     * Closes the current database connection.
     *
     * @return void
     */
    public function disconnect(): void
    {
        if (!$this->isConnected()) {
            return;
        }

        $this->pdo = null;
    }

    /**
     * Begins a new transaction.
     *
     * @return bool True on success.
     * @throws Exception If not connected to the database.
     */
    public function beginTransaction(): bool
    {
        if (!$this->isConnected()) {
            throw new Exception("Not connected to DB.");
        }
        return $this->pdo->beginTransaction();
    }

    /**
     * Commits the current transaction.
     *
     * @return bool True on success.
     * @throws Exception If not connected to the database.
     */
    public function commit(): bool
    {
        if (!$this->isConnected()) {
            throw new Exception("Not connected to DB.");
        }
        return $this->pdo->commit();
    }

    /**
     * Rolls back the current transaction.
     *
     * @return bool True on success.
     * @throws Exception If not connected to the database.
     */
    public function rollBack(): bool
    {
        if (!$this->isConnected()) {
            throw new Exception("Not connected to DB.");
        }
        return $this->pdo->rollBack();
    }

    /**
     * Returns the ID of the last inserted row.
     *
     * @return string The last inserted ID.
     * @throws Exception If not connected to the database.
     */
    public function lastInsertId(): string
    {
        if (!$this->isConnected()) {
            throw new Exception("Not connected to DB.");
        }
        return $this->pdo->lastInsertId();
    }
}
