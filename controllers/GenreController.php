<?php

require_once __DIR__ . "/BaseController.php";
require_once dirname(__DIR__) . "/repositories/GenreRepository.php";
require_once dirname(__DIR__) . "/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/Database.php";
require_once dirname(__DIR__) . "/exceptions/GenreNotFound.php";

/**
 * Handles the listing and detail views of genres.
 */
class GenreController extends BaseController
{
    private Database $db;
    private GenreRepository $genreRepository;
    private ArtworkRepository $artworkRepository;

    /**
     * Initializes Database connection and repositories for genres and artworks.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->genreRepository = new GenreRepository($this->db);
        $this->artworkRepository = new ArtworkRepository($this->db);
    }

    /**
     * Displays a list of all genres.
     * Starts session if not started.
     * Fetches all genres from the repository.
     * Passes the genres and page title to the view.
     */
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $genres = $this->genreRepository->getAllGenres();

        $data = [
            'genres' => $genres,
            'title' => 'Genres - Art Gallery'
        ];

        $this->renderWithLayout('genres/index', $data);
    }

    /**
     * Displays details for a specific genre and its associated artworks.
     * 
     * @param int|string $id Genre ID to display.
     * 
     * Validates the ID, fetches genre and its artworks.
     * 
     * @throws HttpException if invalid ID or genre not found.
     */
    public function show($id): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Validate genre ID parameter
        if (!$id || !is_numeric($id)) {
            throw new HttpException(400, "The genre ID parameter is invalid or missing.");
        }

        $genreId = (int) $id;

        try {
            // Attempt to fetch genre by ID
            $genre = $this->genreRepository->getGenreById($genreId);

            // Fetch artworks belonging to the genre
            $artworks = $this->artworkRepository->getArtworksByGenre($genreId);

            $data = [
                'genre' => $genre,
                'artworks' => $artworks,
                'title' => $genre->getGenreName() . ' - Genres'
            ];

            // Render the genre details page with artworks
            $this->renderWithLayout('genres/show', $data);

        } catch (GenreNotFoundException $e) {
            throw new HttpException(404, $e->getMessage());
        }
    }
}
