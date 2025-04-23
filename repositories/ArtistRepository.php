<?php

require_once dirname(__DIR__)."/Database.php";
require_once dirname(__DIR__)."/classes/Artist.php";

class ArtistRepository {
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    /**
     * @return Artist[]
    */
    public function getAllArtists(bool $sortDesc) : array
    {
        $this->db->connect();

        // Checks if input parameter is set to descending
        $sortOrder = "DESC" ? $sortDesc : "ASC";

        $sql = "SELECT * FROM artists ORDER BY LastName {$sortOrder}, FirstName {$sortOrder}";
        
        $stmt = $this->db->prepareStatement($sql);
        $stmt->execute();

        $artists = [];

        foreach ($stmt as $row) {
            $artists[] = Artist::createArtistFromRecord($row);
        }

        $this->db->disconnect();

        return $artists;
    }

    public function findMostReviewed(int $n = 3): ArtistWithStatsArray
    {
        $this->db->connect();

        $sql = "
            select a.*, count(r.ReviewId) review_count
            from artists a
            join artworks aw on aw.ArtistID = a.ArtistID
            join reviews r on r.ArtWorkId = aw.ArtWorkID
            group by a.ArtistID
            order by review_count desc
            limit :n
        ";

        // use prepared statement
        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue("n", $n, PDO::PARAM_INT); // without type n is inserted as string
        $stmt->execute();

        $mostReviewedArtists = new ArtistWithStatsArray();

        foreach ($stmt as $row)
        {
            $artist = Artist::createArtistFromRecord($row);
            $reviewCount = $row['review_count'];

            $mostReviewedArtists[] = new ArtistWithStats($artist, $reviewCount);
        }

        $this->db->disconnect();

        return $mostReviewedArtists;
    }


    /**
    * @throws Exception if artist couldn't be found
    */
    public function getArtistById(int $artistId) : Artist
    {
        $this->db->connect();

        $sql = "SELECT * FROM artists WHERE ArtistId = :id";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue("id", $artistId, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();

        $this->db->disconnect();

        if (isset($row))
        {
            return Artist::createArtistfromRecord($row);
        }
        else
        {
            throw new Exception("Artist with ID {$artistId} couldn't be found");
        }
    }
}