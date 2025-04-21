<?php

require_once dirname(__DIR__)."/Database.php";
require_once dirname(__DIR__)."/classes/Artist.php";

class ArtistRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAllArtists($sort) : array {
        // Checks if input parameter is valid set to ascending
        if (!in_array($sort, ['ascending', 'descending'])) {
            $sort = 'ascending';
        }
        switch ($sort) {
            case 'descending':
                $sql = "SELECT * FROM artists ORDER BY LastName DESC, FirstName DESC";
                break;
            case 'ascending':
                $sql = "SELECT * FROM artists ORDER BY LastName ASC, FirstName ASC";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $artists = [];
        foreach ($stmt as $row) {
            $artists[] = new Artist($row);
        }

        return $artists;
    }
}