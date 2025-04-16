<?php
require_once dirname(__DIR__)."/src/env.php";

// Use Singleton design pattern:
// https://refactoring.guru/design-patterns/singleton

// Better practice would be to implement a "Connection Factory"
// Since this app will not be used by real users and wont come to the point of requiring multiple connections
// a singleton will be sufficient:
// https://stackoverflow.com/questions/130878/global-or-singleton-for-database-connection?rq=3
class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
		try
		{
			$host = $_ENV['DB_HOST'];
			$user = $_ENV['DB_USERNAME'];
			$pass = $_ENV['DB_PASSWORD'];
			$dbname = $_ENV['DB_NAME'];

			$connectionString = "mysql:host=" . $host . ";dbname=" . $dbname;

			$this->pdo = new PDO($connectionString, $user, $pass);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch (PDOException $e)
		{
			$this->pdo;
			exit("Database connection failed: " . $e->getMessage());
		}
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}
