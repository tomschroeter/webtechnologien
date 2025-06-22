<?php

require_once dirname(__DIR__) . "/Database.php";
require_once dirname(__DIR__) . "/classes/Artwork.php";
require_once dirname(__DIR__) . "/classes/Gallery.php";
require_once dirname(__DIR__) . "/repositories/ArtistRepository.php";
require_once dirname(__DIR__) . "/repositories/SubjectRepository.php";
require_once dirname(__DIR__) . "/repositories/GenreRepository.php";
require_once dirname(__DIR__) . "/dtos/ArtworkWithArtistName.php";
require_once dirname(__DIR__) . "/components/fix-file-path.php";
require_once dirname(__DIR__) . "/exceptions/ArtworkNotFound.php";

class ArtworkRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Get all artworks with optional sorting.
     *
     * @param string $sortBy Field to sort by ('title', 'artist', 'year').
     * @param string $sortOrder Sort direction ('asc' or 'desc').
     * @return Artwork[] Array of Artwork objects.
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
            $row['ImageFileName'] = fixFilePath(($row['ImageFileName']));

            $artworks[] = Artwork::createArtworkFromRecord($row);
        }

        $this->db->disconnect();

        return $artworks;
    }

    /**
     * Retrieve a single artwork by its ID.
     *
     * @param int $id The ID of the artwork.
     * @return Artwork The retrieved artwork.
     * @throws ArtworkNotFoundException If artwork not found.
     */
    public function getArtworkById(int $id): Artwork
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "SELECT *
        FROM artworks
        WHERE ArtWorkID = :id
        ";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();

        $artwork = $stmt->fetch();

        if ($artwork === false) {
            $this->db->disconnect();
            throw new ArtworkNotFoundException($id);
        }

        // Add 0 in front of image file name if name is 5 characters long
        $artwork['ImageFileName'] = fixFilePath($artwork['ImageFileName']);

        $this->db->disconnect();

        return Artwork::createArtworkFromRecord($artwork);
    }

    /**
     * Get all artworks created by a specific artist.
     *
     * @param int $artistId ID of the artist.
     * @return Artwork[] Array of Artwork objects.
     * @throws ArtistNotFoundException If artist ID does not exist.
     */
    public function getArtworksByArtist(int $artistId): array
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "SELECT *
        FROM artworks, artists
        WHERE artworks.ArtistID = artists.ArtistID
        AND artists.ArtistID = :id
        ";

        $stmt = $this->db->prepareStatement($sql);

        // Checks if artist with given ID exists (can throw ArtistNotFoundException)
        $artistRepository = new ArtistRepository($this->db);
        $artistRepository->getArtistById($artistId);

        $stmt->bindValue("id", $artistId);
        $stmt->execute();

        $artworks = [];

        foreach ($stmt as $row) {

            // Add 0 in front of image file name if name is 5 characters long
            $row['ImageFileName'] = fixFilePath($row['ImageFileName']);

            $artworks[] = Artwork::createArtworkFromRecord($row);
        }

        $this->db->disconnect();

        return $artworks;
    }

    /**
     * Get all artworks associated with a specific subject.
     *
     * @param int $subjectId ID of the subject.
     * @return Artwork[] Array of Artwork objects.
     * @throws SubjectNotFoundException If subject ID does not exist.
     */
    public function getArtworksBySubject(int $subjectId): array
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "SELECT *
        FROM artworks, subjects, artworksubjects
        WHERE artworks.ArtworkID = artworksubjects.ArtworkID
        AND artworksubjects.SubjectID = subjects.SubjectID
        AND subjects.SubjectId = :id
        ";

        $stmt = $this->db->prepareStatement($sql);

        // Checks if subject with given ID exists (will throw SubjectNotFoundException if not found)
        $subjectRepository = new SubjectRepository($this->db);
        $subjectRepository->getSubjectById($subjectId);

        $stmt->bindValue("id", $subjectId);
        $stmt->execute();

        $artworks = [];

        foreach ($stmt as $row) {

            // Add 0 in front of image file name if name is 5 characters long
            $row['ImageFileName'] = fixFilePath($row['ImageFileName']);

            $artworks[] = Artwork::createArtworkFromRecord($row);
        }

        $this->db->disconnect();

        return $artworks;
    }

    /**
     * Get all artworks associated with a specific genre.
     *
     * @param int $genreId ID of the genre.
     * @return Artwork[] Array of Artwork objects.
     * @throws GenreNotFoundException If genre ID does not exist.
     */
    public function getArtworksByGenre(int $genreId): array
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "SELECT *
        FROM artworks, genres, artworkgenres
        WHERE artworks.ArtworkID = artworkgenres.ArtworkID
        AND artworkgenres.GenreID = genres.GenreID
        AND genres.GenreID = :id
        ";

        $stmt = $this->db->prepareStatement($sql);

        // Checks if genre with given ID exists (will throw GenreNotFoundException if not found)
        $genreRepository = new GenreRepository($this->db);
        $genreRepository->getGenreById($genreId);

        $stmt->bindValue("id", $genreId);
        $stmt->execute();

        $artworks = [];

        foreach ($stmt as $row) {
            // Add 0 in front of image file name if name is 5 characters long
            $row['ImageFileName'] = fixFilePath($row['ImageFileName']);

            $artworks[] = Artwork::createArtworkFromRecord($row);
        }

        $this->db->disconnect();

        return $artworks;
    }

    /**
     * Get top-rated artworks based on average rating and number of reviews.
     *
     * @return ArtworkWithRatingAndArtistNameArray Array of artworks with rating and artist name.
     */
    public function getTopRatedArtworks(): ArtworkWithRatingAndArtistNameArray
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "SELECT a.*, ar.FirstName, ar.LastName, AVG(r.Rating) AS AvgRating, COUNT(r.ReviewId) as reviewCount
        FROM reviews r
        JOIN artworks a ON r.ArtWorkId = a.ArtWorkId
        JOIN artists ar ON a.ArtistId = ar.ArtistId
        GROUP BY a.ArtWorkId, a.Title, ar.FirstName, ar.LastName
        HAVING COUNT(r.ReviewId) >= 3
        ORDER BY AvgRating DESC
        LIMIT 3
       ";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->execute();

        $artworksWithRating = new ArtworkWithRatingAndArtistNameArray();

        foreach ($stmt as $row) {
            // Add 0 in front of image file name if name is 5 characters long
            $row['ImageFileName'] = fixFilePath($row['ImageFileName']);
            $rating = $row['AvgRating'];
            $reviewCount = $row['reviewCount'];
            $artistFirstName = $row['FirstName'];
            $artistLastName = $row['LastName'];
            $artwork = Artwork::createArtworkFromRecord($row);
            $artworksWithRating[] = new ArtworkWithRatingAndArtistName($artwork, $artistFirstName, $artistLastName, $rating, $reviewCount);
        }

        $this->db->disconnect();

        return $artworksWithRating;
    }

    /**
     * Search artworks by a title keyword with optional sorting.
     *
     * @param string $searchQuery Keyword to search in artwork titles.
     * @param string $sortParameter Field to sort by ('title', 'lastname', 'yearofwork').
     * @param bool $sortDesc Whether to sort in descending order.
     * @return ArtworkWithArtistNameArray Array of artworks with artist names.
     */
    public function getArtworkBySearchQuery(string $searchQuery, string $sortParameter, bool $sortDesc): ArtworkWithArtistNameArray
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

        $artworks = new ArtworkWithArtistNameArray();

        foreach ($stmt as $row) {
            // Add 0 in front of image file name if name is 5 characters long
            $row['ImageFileName'] = fixFilePath($row['ImageFileName']);

            $artwork = Artwork::createArtworkFromRecord($row);
            $artistFirstName = $row['FirstName'];
            $artistLastName = $row['LastName'];
            $artworks[] = new ArtworkWithArtistName($artwork, $artistFirstName, $artistLastName);
        }

        $this->db->disconnect();

        return $artworks;
    }

    /**
     * Perform an advanced search for artworks with optional filters and sorting.
     *
     * @param ?string $title Partial title to filter by.
     * @param ?int $startYear Start year for filtering.
     * @param ?int $endYear End year for filtering.
     * @param ?string $genreName Genre name to filter by.
     * @param string $sortParameter Field to sort by ('title', 'lastname', 'yearofwork').
     * @param bool $sortDesc Whether to sort in descending order.
     * @return ArtworkWithArtistNameArray Array of artworks matching search criteria.
     */
    public function getArtworksByAdvancedSearch($title = null, $startYear = null, $endYear = null, $genreName = null, string $sortParameter, bool $sortDesc): ArtworkWithArtistNameArray
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        // Mapping of sort parameter to prevent SQL injections (whitelist approach)
        $sortMap = [
            'title' => 'a.Title',
            'lastname' => 'ar.LastName',
            'yearofwork' => 'a.YearOfWork'
        ];
        $sortField = $sortMap[strtolower($sortParameter)] ?? 'a.Title';
        $sortOrder = $sortDesc ? "DESC" : "ASC";

        // Build the base SQL query
        $sql = "SELECT DISTINCT a.*, ar.FirstName, ar.LastName
                FROM artworks a
                INNER JOIN artists ar ON a.ArtistID = ar.ArtistID";

        $params = [];

        // Conditionally join with genres if genre filter is provided
        if (!empty($genreName)) {
            $sql .= " INNER JOIN artworkgenres ag ON a.ArtWorkID = ag.ArtWorkID";
            $sql .= " INNER JOIN genres g ON ag.GenreID = g.GenreID";
        }

        $sql .= " WHERE 1=1";

        // Add conditions with named parameter binding
        if (!empty($title)) {
            $sql .= " AND a.Title LIKE :title";
            $params['title'] = "%" . $title . "%";
        }

        if (!empty($genreName)) {
            $sql .= " AND g.GenreName = :genreName";
            $params['genreName'] = $genreName;
        }

        if (!empty($startYear)) {
            $sql .= " AND (a.YearOfWork >= :startYear OR a.YearOfWork IS NULL)";
            $params['startYear'] = (int) $startYear;
        }

        if (!empty($endYear)) {
            $sql .= " AND (a.YearOfWork <= :endYear OR a.YearOfWork IS NULL)";
            $params['endYear'] = (int) $endYear;
        }

        // Ordering appended at the end
        $sql .= " ORDER BY {$sortField} {$sortOrder}";

        $stmt = $this->db->prepareStatement($sql);

        $stmt->execute($params);

        $artworks = new ArtworkWithArtistNameArray();

        foreach ($stmt as $row) {
            $row['ImageFileName'] = fixFilePath($row['ImageFileName']);

            $artwork = Artwork::createArtworkFromRecord($row);
            $artistFirstName = $row['FirstName'];
            $artistLastName = $row['LastName'];

            $artworks[] = new ArtworkWithArtistName($artwork, $artistFirstName, $artistLastName);
        }

        $this->db->disconnect();

        return $artworks;
    }
}
