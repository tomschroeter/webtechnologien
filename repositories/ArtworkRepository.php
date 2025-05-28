<?php

require_once dirname(__DIR__)."/Database.php";
require_once dirname(__DIR__)."/classes/Artwork.php";
require_once dirname(__DIR__)."/repositories/ArtistRepository.php";
require_once dirname(__DIR__)."/dtos/ArtworkWithArtistName.php";

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
    public function getAllArtworks($sortBy = 'title', $sortOrder = 'asc')
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

        return $artworks;
    }
}
