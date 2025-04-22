<?php

require_once dirname(__DIR__)."/Database.php";
require_once dirname(__DIR__)."/classes/Artist.php";

class ArtistRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * @return Artist[]
    */
    public function getAllArtists(bool $sortDesc) : array {
        // Checks if input parameter is set to descending
        if ($sortDesc) {
            $sortOrder = 'DESC';
        } else {
            $sortOrder = 'ASC';
        }
        
        $sql = "SELECT * FROM artists ORDER BY LastName {$sortOrder}, FirstName {$sortOrder}";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $artists = [];
        foreach ($stmt as $row) {
            $artists[] = Artist::createArtistFromRecord($row);
        }

        return $artists;
    }

    public function findMostReviewed(int $n = 3): ArtistWithStatsArray
    {
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
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue("n", $n, PDO::PARAM_INT); // without type n is inserted as string
        $stmt->execute();


        $mostReviewedArtists = new ArtistWithStatsArray();

        foreach ($stmt as $row)
        {
            $artist = Artist::createArtistFromRecord($row);
            $reviewCount = $row['review_count'];

            $mostReviewedArtists[] = new ArtistWithStats($artist, $reviewCount);
        }

        return $mostReviewedArtists;
    }
}