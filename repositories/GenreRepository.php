<?php

require_once dirname(__DIR__)."/Database.php";
require_once dirname(__DIR__)."/classes/Genre.php";

class GenreRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @return Genre[]
    */
    public function getAllGenres(): array
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "SELECT * FROM genres ORDER BY era, genreName ASC;";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->execute();

        $genres = [];

        foreach ($stmt as $row) {
            $genres[] = Genre::createGenreFromRecord($row);
        }

        $this->db->disconnect();

        return $genres;
    }
}
