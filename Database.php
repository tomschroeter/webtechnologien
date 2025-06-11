<?php

require_once dirname(__DIR__)."/src/env.php";

// https://elearning.th-wildau.de/mod/resource/view.php?id=490348
class Database
{
    private $pdo;

    public function connect()
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
            $this->pdo;
            exit("Database connection failed: " . $e->getMessage());
        }
    }

    public function prepareStatement(string $sql)
    {
        if (!$this->isConnected()) {
            throw new Exception("Not connected to DB.");
        }

        return $this->pdo->prepare($sql);
    }

    public function isConnected(): bool
    {
        return $this->pdo != null;
    }

    public function disconnect()
    {
        if (!$this->isConnected()) {
            return;
        }

        $this->pdo = null;
    }

    public function beginTransaction(): bool
    {
        if (!$this->isConnected()) {
            throw new Exception("Not connected to DB.");
        }
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        if (!$this->isConnected()) {
            throw new Exception("Not connected to DB.");
        }
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        if (!$this->isConnected()) {
            throw new Exception("Not connected to DB.");
        }
        return $this->pdo->rollBack();
    }

    public function lastInsertId(): string
    {
        if (!$this->isConnected()) {
            throw new Exception("Not connected to DB.");
        }
        return $this->pdo->lastInsertId();
    }
}
