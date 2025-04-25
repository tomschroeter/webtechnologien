<?php

require_once dirname(__DIR__)."/Database.php";
require_once dirname(__DIR__)."/classes/Artwork.php";
require_once dirname(__DIR__)."/repositories/ArtistRepository.php";

class ArtworkRepository {
    private Database $db;
    private ArtistRepository $artistRepository;

    public function __construct(Database $db) {
        $this->db = $db;
        $this->artistRepository = new ArtistRepository($db);
    }

    public function findById(int $id) : Artwork
    {
        if (!$this->db->isConnected()) $this->db->connect();

        $sql = "
            select *
            from artworks
            where ArtWorkID = :id
        ";

        // use prepared statement
        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();

        $artwork = $stmt->fetch();

        $this->db->disconnect();

        return Artwork::createArtworkFromRecord($artwork);
    }

    /**
    * @return Artwork[]
    */
    public function getArtworksByArtist(int $artistId) : array
    {
        if (!$this->db->isConnected()) $this->db->connect();

        $sql = "
            SELECT *
            FROM artworks, artists
            WHERE artworks.ArtistID = artists.ArtistID
            AND artists.ArtistID = :id
        ";

        $stmt = $this->db->prepareStatement($sql);

        // Checks if artist with given ID exists
        $this->artistRepository->getArtistById($artistId);

        $stmt->bindValue("id", $artistId);
        $stmt->execute();

        $artworks = [];

        foreach ($stmt as $row) {

            // Add 0 in front of image file name if name is 5 characters long
            if (strlen($row['ImageFileName']) < 6) {
                $row['ImageFileName'] = '0' . $row['ImageFileName'];
            }

            $artworks[] = Artwork::createArtworkFromRecord($row);
        }

        $this->db->disconnect();

        return $artworks;
    }
}