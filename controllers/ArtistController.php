<?php

require_once __DIR__ . "/BaseController.php";
require_once dirname(__DIR__) . "/repositories/ArtistRepository.php";
require_once dirname(__DIR__) . "/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/Database.php";
require_once dirname(__DIR__) . "/components/find-image-ref.php";

/**
 * Handles the listing and detail views of artists and their artworks.
 */
class ArtistController extends BaseController
{
    private Database $db;
    private ArtistRepository $artistRepository;

    private ArtworkRepository $artworkRepository;

    /**
     * Initializes database connection and required repositories.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->artistRepository = new ArtistRepository($this->db);
        $this->artworkRepository = new ArtworkRepository($this->db);
    }

    /**
     * Displays a list of all artists.
     * Supports optional sorting by name in descending order via the 'sort' query parameter.
     */
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Determine sort order based on query parameter
        $sort = isset($_GET['sort']) && $_GET['sort'] === 'descending';

        // Retrieve all artists from the repository
        $artists = $this->artistRepository->getAllArtists($sort);

        $data = [
            'artists' => $artists,
            'sort' => $sort,
            'title' => 'Artists - Art Gallery'
        ];

        $this->renderWithLayout('artists/index', $data);
    }

    /**
     * Displays details of a specific artist and their artworks.
     *
     * @param int|string $id The ID of the artist to display.
     *
     * @throws HttpException if the ID is invalid, artist not found, or a database error occurs.
     */
    public function show($id): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Validate the artist ID
        if (!$id || !is_numeric($id)) {
            throw new HttpException(400, "The artist ID parameter is invalid or missing.");
        }

        $artistId = (int) $id;

        try {
            // Fetch the artist by ID
            $artist = $this->artistRepository->getArtistById($artistId);

            if (!$artist) {
                throw new HttpException(404, "No artist with the given ID was found.");
            }

            // Fetch artworks by the artist
            $artworks = $this->artworkRepository->getArtworksByArtist($artistId);

            $data = [
                'artist' => $artist,
                'artworks' => $artworks,
                'title' => $artist->getFullName() . ' - Artists'
            ];

            $this->renderWithLayout('artists/show', $data);

        } catch (Exception $e) {
            error_log("Error loading artist: " . $e->getMessage());
            throw new HttpException(500, "A database error occurred while loading the artist. Please try again later.");
        }
    }
}