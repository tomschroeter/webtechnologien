<?php

require_once __DIR__ . "/BaseController.php";
require_once dirname(__DIR__) . "/repositories/ArtistRepository.php";
require_once dirname(__DIR__) . "/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/repositories/ReviewRepository.php";
require_once dirname(__DIR__) . "/Database.php";

/**
 * Handles the view of home page and About Us page.
 */
class HomeController extends BaseController
{
    private Database $db;
    private ArtistRepository $artistRepository;
    private ArtworkRepository $artworkRepository;
    private ReviewRepository $reviewRepository;

    /*
     * Initialize database connection and repositories.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->artistRepository = new ArtistRepository($this->db);
        $this->artworkRepository = new ArtworkRepository($this->db);
        $this->reviewRepository = new ReviewRepository($this->db);
    }

    /**
     * Render the home page.
     * Starts a session if not already started.
     * Passes page title to the view.
     */
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $data = [
            'title' => 'Home - Art Gallery'
        ];

        // Render the home/index view within the main layout
        $this->renderWithLayout('home/index', $data);
    }

    /**
     * Render the "About Us" page.
     * Starts a session if not already started.
     * Passes contributors and page title to the view.
     */
    public function about()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Load contributors data
        require_once dirname(__DIR__) . "/components/contributor-list.php";

        $data = [
            'title' => 'About Us - Art Gallery',
            'contributors' => $contributors
        ];

        // Render the home/about view within the main layout
        $this->renderWithLayout('home/about', $data);
    }
}
