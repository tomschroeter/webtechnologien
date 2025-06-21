<?php

require_once __DIR__ . "/BaseController.php";
require_once dirname(__DIR__) . "/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/repositories/ArtistRepository.php";
require_once dirname(__DIR__) . "/repositories/ReviewRepository.php";
require_once dirname(__DIR__) . "/Database.php";
require_once dirname(__DIR__) . "/components/find-image-ref.php";

/**
 * Handles actions related to artwork listings and details.
 */
class ArtworkController extends BaseController
{
    private Database $db;
    private ArtworkRepository $artworkRepository;
    private ArtistRepository $artistRepository;
    private ReviewRepository $reviewRepository;

    /**
     * Initializes the database and repositories.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->artworkRepository = new ArtworkRepository($this->db);
        $this->artistRepository = new ArtistRepository($this->db);
        $this->reviewRepository = new ReviewRepository($this->db);
    }

    /**
     * Displays a list of all artworks.
     * Supports sorting by 'title', 'artist', or 'year' using `sort` and `order` query parameters.
     */
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Retrieve and validate sorting parameters
        $sortBy = $_GET['sort'] ?? 'title';
        $sortOrder = $_GET['order'] ?? 'asc';

        $validSortFields = ['title', 'artist', 'year'];
        if (!in_array($sortBy, $validSortFields)) {
            $sortBy = 'title';
        }

        $validSortOrders = ['asc', 'desc'];
        if (!in_array($sortOrder, $validSortOrders)) {
            $sortOrder = 'asc';
        }

        // Get artworks from repository
        $artworks = $this->artworkRepository->getAllArtworks($sortBy, $sortOrder);

        // Attach artist names to artworks
        foreach ($artworks as $artwork) {
            try {
                $artist = $this->artistRepository->getArtistById($artwork->getArtistID());
                $artwork->artistName = $artist ? $artist->getFullName() : 'Unknown Artist';
            } catch (Exception $e) {
                $artwork->artistName = 'Unknown Artist';
            }
        }

        $data = [
            'artworks' => $artworks,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'title' => 'Artworks - Art Gallery'
        ];

        $this->renderWithLayout('artworks/index', $data);
    }

    /**
     * Displays detailed information about a specific artwork.
     * Also retrieves the associated artist and customer reviews.
     *
     * @param int|string $id The ID of the artwork to display.
     *
     * @throws HttpException if the ID is invalid, the artwork is not found,
     * or a database error occurs.
     */
    public function show($id): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Validate the artwork ID
        if (!$id || !is_numeric($id)) {
            throw new HttpException(400, "The artwork ID parameter is invalid or missing.");
        }

        $artworkId = (int) $id;

        // Retrieve artwork, artist and review data
        try {
            $artwork = $this->artworkRepository->findById($artworkId);

            if (!$artwork) {
                throw new HttpException(404, "No artwork with the given ID was found.");
            }

            $artist = $this->artistRepository->getArtistById($artwork->getArtistId());
            $reviews = $this->reviewRepository->getAllReviewsWithCustomerInfo($artworkId);

            $data = [
                'artwork' => $artwork,
                'artist' => $artist,
                'reviews' => $reviews,
                'title' => $artwork->getTitle() . ' - Artworks'
            ];

            $this->renderWithLayout('artworks/show', $data);

        } catch (HttpException $e) {
            throw $e;
        } catch (Exception $e) {
            error_log("Error loading artwork: " . $e->getMessage());
            throw new HttpException(500, "A database error occurred while loading the artwork. Please try again later.");
        }
    }
}