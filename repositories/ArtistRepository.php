<?php

require_once dirname(__DIR__) . "/Database.php";
require_once dirname(__DIR__) . "/classes/Artist.php";
require_once dirname(__DIR__) . "/dtos/ArtistWithStats.php";

class ArtistRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @return Artist[]
     */
    public function getAllArtists(bool $sortDesc): array
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        // Checks if input parameter is set to descending
        $sortOrder = $sortDesc ? "DESC" : "ASC";

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
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

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

        foreach ($stmt as $row) {
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
    public function getArtistById(int $artistId): Artist
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "SELECT * FROM artists WHERE ArtistId = :id";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue("id", $artistId, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();

        $this->db->disconnect();

        if ($row !== false) {
            return Artist::createArtistfromRecord($row);
        } else {
            throw new Exception("Artist with ID {$artistId} couldn't be found");
        }
    }

    /**
     * Summary of getArtistBySearchQuery
     * @param string $searchQuery
     * @param bool $sortDesc
     * @return Artist[]
     */
    public function getArtistBySearchQuery(string $searchQuery, bool $sortDesc): array
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sortOrder = $sortDesc ? "DESC" : "ASC";

        $sql = "SELECT * FROM artists WHERE LastName LIKE :searchQuery ORDER BY LastName {$sortOrder}";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue('searchQuery', '%' . $searchQuery . '%');
        $stmt->execute();

        $artists = [];

        foreach ($stmt as $row) {
            $artists[] = Artist::createArtistFromRecord($row);
        }

        $this->db->disconnect();

        return $artists;
    }

    /**
     * Summary of getArtistNationalities
     * @return array
     */
    public function getArtistNationalities()
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "
        SELECT DISTINCT nationality FROM artists 
        WHERE nationality IS NOT NULL 
        ORDER BY nationality;
        ";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->execute();

        $nationalities = [];

        foreach ($stmt as $row) {
            $nationalities[] = $row['nationality']; // Fixed: use [] instead of ::append() and access the column
        }

        $this->db->disconnect();

        return $nationalities;
    }

    /**
     * Summary of getArtistByAdvancedSearch
     * @param mixed $name
     * @param mixed $startYear
     * @param mixed $endYear
     * @param mixed $nationality
     * @param bool $sortDesc
     * @return Artist[]
     */
    public function getArtistByAdvancedSearch($name = null, $startYear = null, $endYear = null, $nationality = null, bool $sortDesc)
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "SELECT * FROM artists WHERE 1=1";
        $params = [];

        if (!empty($name)) {
            $nameParts = preg_split('/\s+/', trim($name));
            $i = 0;
            foreach ($nameParts as $part) {
                $sql .= " AND (FirstName LIKE :namePart{$i} OR LastName LIKE :namePart{$i})";
                $params["namePart{$i}"] = '%' . $part . '%';
                $i++;
            }
        }

        if (!empty($nationality)) {
            $sql .= " AND Nationality = :nationality";
            $params['nationality'] = $nationality;
        }

        if (!empty($startYear)) {
            $sql .= " AND (YearOfBirth >= :startYear OR YearOfBirth IS NULL)";
            $params['startYear'] = (int) $startYear;
        }

        if (!empty($endYear)) {
            $sql .= " AND (YearOfBirth <= :endYear OR YearOfBirth IS NULL)";
            $params['endYear'] = (int) $endYear;
        }

        $sql .= " ORDER BY LastName " . ($sortDesc ? "DESC" : "ASC");

        $stmt = $this->db->prepareStatement($sql);
        $stmt->execute($params);

        $artists = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $artists[] = Artist::createArtistFromRecord($row);
        }

        $this->db->disconnect();

        return $artists;
    }

}
