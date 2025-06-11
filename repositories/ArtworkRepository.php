<?php

require_once dirname(__DIR__) . "/Database.php";
require_once dirname(__DIR__) . "/classes/Artwork.php";
require_once dirname(__DIR__) . "/classes/Gallery.php";
require_once dirname(__DIR__) . "/repositories/ArtistRepository.php";
require_once dirname(__DIR__) . "/repositories/SubjectRepository.php";
require_once dirname(__DIR__) . "/repositories/GenreRepository.php";
require_once dirname(__DIR__) . "/dtos/ArtworkWithArtistName.php";

class ArtworkRepository
{
    private Database $db;
    private ArtistRepository $artistRepository;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->artistRepository = new ArtistRepository($db);
    }

    public function findById(int $id): Artwork
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

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

        // Check if artwork was found
        if ($artwork === false) {
            $this->db->disconnect();
            throw new Exception("Artwork with ID {$id} not found");
        }

        // Add 0 in front of image file name if name is 5 characters long
        if (strlen($artwork['ImageFileName']) < 6) {
            $artwork['ImageFileName'] = '0' . $artwork['ImageFileName'];
        }

        $this->db->disconnect();

        return Artwork::createArtworkFromRecord($artwork);
    }

    /**
     * @return Artwork[]
     */
    public function getArtworksByArtist(int $artistId): array
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

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

    /**
     * @return Artwork[]
     */
    public function getArtworksBySubject(int $subjectId): array
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "
            SELECT *
            FROM artworks, subjects, artworksubjects
            WHERE artworks.ArtworkID = artworksubjects.ArtworkID
            AND artworksubjects.SubjectID = subjects.SubjectID
            AND subjects.SubjectId = :id
        ";

        $stmt = $this->db->prepareStatement($sql);

        // Checks if subject with given ID exists (will throw exception if not found)
        $subjectRepository = new SubjectRepository($this->db);
        $subjectRepository->getSubjectById($subjectId);

        $stmt->bindValue("id", $subjectId);
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

    /**
     * @return Artwork[]
     */
    public function getArtworksByGenre(int $genreId): array
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "
            SELECT *
            FROM artworks, genres, artworkgenres
            WHERE artworks.ArtworkID = artworkgenres.ArtworkID
            AND artworkgenres.GenreID = genres.GenreID
            AND genres.GenreID = :id
        ";

        $stmt = $this->db->prepareStatement($sql);

        // Checks if genre with given ID exists (will throw exception if not found)
        $genreRepository = new GenreRepository($this->db);
        $genreRepository->getGenreById($genreId);

        $stmt->bindValue("id", $genreId);
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

    /**
     * Summary of getArtworkBySearchQuery
     * @param string $searchQuery
     * @param string $sortParameter
     * @param bool $sortDesc
     * @return ArtworkWithArtistName[]
     */
    public function getArtworkBySearchQuery(string $searchQuery, string $sortParameter, bool $sortDesc): array
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        // Mapping of sort parameter to prevent SQL injections
        $sortMap = [
            'title' => 'artworks.Title',
            'lastname' => 'artists.LastName',
            'yearofwork' => 'artworks.YearOfWork'
        ];
        $sortField = $sortMap[strtolower($sortParameter)] ?? 'artworks.Title';

        $sortOrder = $sortDesc ? "DESC" : "ASC";

        $sql = "SELECT artworks.*, artists.FirstName, artists.LastName
                FROM artworks, artists
                WHERE artworks.ArtistID = artists.ArtistID
                    AND Title LIKE :searchQuery
                ORDER BY {$sortField} {$sortOrder}";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue('searchQuery', '%' . $searchQuery . '%');
        $stmt->execute();

        $artworks = [];

        foreach ($stmt as $row) {
            // Add 0 in front of image file name if name is 5 characters long
            if (strlen($row['ImageFileName']) < 6) {
                $row['ImageFileName'] = '0' . $row['ImageFileName'];
            }

            $artwork = Artwork::createArtworkFromRecord($row);
            $artistFirstName = $row['FirstName'];
            $artistLastName = $row['LastName'];
            $artworks[] = new ArtworkWithArtistName($artwork, $artistFirstName, $artistLastName);
        }

        $this->db->disconnect();

        return $artworks;
    }

    /**
     * Get all artworks with optional sorting
     *
     * @param string $sortBy Field to sort by (title, artist, year)
     * @param string $sortOrder Sort direction (asc, desc)
     * @return array Array of Artwork objects
     */
    public function getAllArtworks($sortBy = 'title', $sortOrder = 'asc'): array
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "";

        switch ($sortBy) {
            case 'artist':
                // Join with artists table to sort by artist name
                $sql = "SELECT a.* FROM artworks a 
                        LEFT JOIN artists ar ON a.ArtistID = ar.ArtistID 
                        ORDER BY ar.LastName " . ($sortOrder === 'desc' ? 'DESC' : 'ASC');
                break;
            case 'year':
                $sql = "SELECT * FROM artworks ORDER BY YearOfWork " . ($sortOrder === 'desc' ? 'DESC' : 'ASC');
                break;
            case 'title':
            default:
                $sql = "SELECT * FROM artworks ORDER BY Title " . ($sortOrder === 'desc' ? 'DESC' : 'ASC');
                break;
        }

        $stmt = $this->db->prepareStatement($sql);
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

    /**
     * Summary of getArtworksByAdvancedSearch
     * @param mixed $title
     * @param mixed $startYear
     * @param mixed $endYear
     * @param mixed $genreName
     * @param string $sortParameter
     * @param bool $sortDesc
     * @return ArtworkWithArtistName[]
     */
    public function getArtworksByAdvancedSearch($title = null, $startYear = null, $endYear = null, $genreName = null, string $sortParameter, bool $sortDesc)
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        // Mapping of sort parameter to prevent SQL injections
        $sortMap = [
            'title' => 'a.Title',
            'lastname' => 'ar.LastName',
            'yearofwork' => 'a.YearOfWork'
        ];
        $sortField = $sortMap[strtolower($sortParameter)] ?? 'Title';

        $sortOrder = $sortDesc ? "DESC" : "ASC";

        // Build the SQL query dynamically
        $sql = "SELECT DISTINCT a.*, ar.FirstName, ar.LastName
            FROM artworks a
            INNER JOIN artists ar ON a.ArtistID = ar.ArtistID";

        // Join with artworkgenres + genres if genre filter is provided
        if (!empty($genreName)) {
            $sql .= " INNER JOIN artworkgenres ag ON a.ArtWorkID = ag.ArtWorkID";
            $sql .= " INNER JOIN genres g ON ag.GenreID = g.GenreID";
        }

        $sql .= " WHERE 1=1";

        if (!empty($title)) {
            $safeTitle = addslashes($title); // or use prepared statement
            $sql .= " AND a.Title LIKE '%$safeTitle%'";
        }

        if (!empty($genreName)) {
            $safeGenre = addslashes($genreName); // or use prepared statement
            $sql .= " AND g.GenreName = '$safeGenre'";
        }

        if (!empty($startYear)) {
            $sql .= " AND (a.YearOfWork >= " . intval($startYear) . " OR a.YearOfWork IS NULL)";
        }

        if (!empty($endYear)) {
            $sql .= " AND (a.YearOfWork <= " . intval($endYear) . " OR a.YearOfWork IS NULL)";
        }

        $sql .= " ORDER BY {$sortField} {$sortOrder}"; //{$sortField} {$sortOrder}

        $stmt = $this->db->prepareStatement($sql);
        $stmt->execute();

        $artworks = [];

        foreach ($stmt as $row) {
            // Add 0 in front of image file name if name is 5 characters long
            if (strlen($row['ImageFileName']) < 6) {
                $row['ImageFileName'] = '0' . $row['ImageFileName'];
            }

            $artwork = Artwork::createArtworkFromRecord($row);
            $artistFirstName = $row['FirstName'];
            $artistLastName = $row['LastName'];

            $artworks[] = new ArtworkWithArtistName($artwork, $artistFirstName, $artistLastName);
        }

        $this->db->disconnect();

        return $artworks;
    }

}
