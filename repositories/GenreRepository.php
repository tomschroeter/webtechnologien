<?php

require_once dirname(__DIR__) . "/Database.php";
require_once dirname(__DIR__) . "/classes/Genre.php";
require_once dirname(__DIR__) . "/exceptions/GenreNotFound.php";

class GenreRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Retrieves all genres from the database, ordered by era and genre name.
     *
     * @return Genre[] An array of Genre objects.
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

    /**
     * Retrieves a genre by its unique ID.
     *
     * @param int $genreId The ID of the genre to retrieve.
     * @return Genre The genre corresponding to the given ID.
     *
     * @throws Exception If the genre is not found in the database.
     */
    public function getGenreById(int $genreId): Genre
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "SELECT * FROM genres WHERE GenreID = :id";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue("id", $genreId, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();

        $this->db->disconnect();

        if ($row !== false) {
            return Genre::createGenreFromRecord($row);
        } else {
            throw new GenreNotFoundException($genreId);
        }
    }

    /**
     * Retrieves all genres associated with a specific artwork.
     *
     * @param int $artworkId The ID of the artwork.
     * @return Genre[] An array of Genre objects linked to the artwork.
     */
    public function getGenresByArtwork(int $artworkId): array
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "
            SELECT g.*
            FROM genres g
            JOIN artworkgenres ag ON g.GenreID = ag.GenreID  
            WHERE ag.ArtworkID = :artworkId
            ORDER BY g.GenreName ASC
        ";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue("artworkId", $artworkId, PDO::PARAM_INT);
        $stmt->execute();

        $genres = [];

        foreach ($stmt as $row) {
            $genres[] = Genre::createGenreFromRecord($row);
        }

        $this->db->disconnect();
        return $genres;
    }
}
