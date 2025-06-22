<?php

require_once dirname(__DIR__) . "/Database.php";
require_once dirname(__DIR__) . "/classes/Artist.php";
require_once dirname(__DIR__) . "/dtos/ArtistWithStats.php";
require_once dirname(__DIR__) . "/exceptions/ArtistNotFound.php";

class ArtistRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Retrieves all artists from the database, sorted by last name and first name.
     *
     * @param bool $sortDesc Whether to sort in descending order (true) or ascending (false).
     * @return Artist[] An array of Artist objects.
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

    /**
     * Retrieves the top N artists based on the number of reviews.
     *
     * @param int $n The number of top-reviewed artists to retrieve.
     * @return ArtistWithStatsArray A typed array of ArtistWithStats objects.
     */
    public function getMostReviewed(int $n): ArtistWithStatsArray
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "SELECT a.*, COUNT(r.ReviewId) AS ReviewCount
        FROM artists a
        JOIN artworks aw ON aw.ArtistID = a.ArtistID
        JOIN reviews r ON r.ArtWorkId = aw.ArtWorkID
        GROUP BY a.ArtistID
        ORDER BY ReviewCount DESC
        LIMIT :n
        ";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue("n", $n, PDO::PARAM_INT);
        $stmt->execute();

        $mostReviewedArtists = new ArtistWithStatsArray();

        foreach ($stmt as $row) {
            $artist = Artist::createArtistFromRecord($row);
            $reviewCount = $row['ReviewCount'];

            $mostReviewedArtists[] = new ArtistWithStats($artist, $reviewCount);
        }

        $this->db->disconnect();

        return $mostReviewedArtists;
    }


    /**
     * Retrieves a single artist by their ID.
     *
     * @param int $artistId The ID of the artist to retrieve.
     * @return Artist The matching Artist object.
     * @throws Exception If no artist is found for the given ID.
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
            throw new ArtistNotFoundException($artistId);
        }
    }

    /**
     * Retrieves artists whose last name matches the search query.
     *
     * @param string $searchQuery The partial string to search for in last names.
     * @param bool $sortDesc Whether to sort results descending by last name.
     * @return Artist[] Array of matching Artist objects.
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
     * Retrieves a list of distinct artist nationalities from the database.
     *
     * @return string[] Array of unique nationalities.
     */
    public function getArtistNationalities(): array
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "
        SELECT DISTINCT Nationality FROM artists 
        WHERE Nationality IS NOT NULL 
        ORDER BY Nationality;
        ";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->execute();

        $nationalities = [];

        foreach ($stmt as $row) {
            $nationalities[] = $row['Nationality'];
        }

        $this->db->disconnect();

        return $nationalities;
    }

    /**
     * Performs an advanced search for artists based on optional filters.
     *
     * @param string|null $name The artist name to match (first and/or last name).
     * @param int|null $startYear Minimum birth year filter.
     * @param int|null $endYear Maximum birth year filter.
     * @param string|null $nationality Nationality to filter by.
     * @param bool $sortDesc Whether to sort results descending by last name.
     * @return Artist[] Array of artists matching the filter criteria.
     */
    public function getArtistByAdvancedSearch($name = null, $startYear = null, $endYear = null, $nationality = null, bool $sortDesc): array
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        // Initialize SQL query with WHERE 1=1 to simplify appending conditions
        $sql = "SELECT * FROM artists WHERE 1=1";
        $params = [];

        // Append name condition if provided, filtering by first and last name
        if (!empty($name)) {
            $nameParts = preg_split('/\s+/', trim($name));
            $i = 0;
            foreach ($nameParts as $part) {
                $sql .= " AND (FirstName LIKE :namePart{$i} OR LastName LIKE :namePart{$i})";
                $params["namePart{$i}"] = '%' . $part . '%';
                $i++;
            }
        }

        // Append nationality condition if provided
        if (!empty($nationality)) {
            $sql .= " AND Nationality = :nationality";
            $params['nationality'] = $nationality;
        }

        // Append start year range condition if provided, NULL is allowed
        if (!empty($startYear)) {
            $sql .= " AND (YearOfBirth >= :startYear OR YearOfBirth IS NULL)";
            $params['startYear'] = (int) $startYear;
        }

        // Append end year range condition if provided, NULL is allowed
        if (!empty($endYear)) {
            $sql .= " AND (YearOfBirth <= :endYear OR YearOfBirth IS NULL)";
            $params['endYear'] = (int) $endYear;
        }

        // Ordering appended at the end
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
