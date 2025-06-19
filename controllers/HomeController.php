<?php

require_once __DIR__ . "/BaseController.php";
require_once dirname(__DIR__) . "/repositories/ArtistRepository.php";
require_once dirname(__DIR__) . "/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/repositories/ReviewRepository.php";
require_once dirname(__DIR__) . "/Database.php";

class HomeController extends BaseController
{
    private $db;
    private $artistRepository;
    private $artworkRepository;
    private $reviewRepository;
    
    public function __construct()
    {
        $this->db = new Database();
        $this->artistRepository = new ArtistRepository($this->db);
        $this->artworkRepository = new ArtworkRepository($this->db);
        $this->reviewRepository = new ReviewRepository($this->db);
    }
    
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Get flash message if any
        $flashMessage = $this->getFlashMessage();
        
        $data = [
            'flashMessage' => $flashMessage,
            'title' => 'Home - Art Gallery'
        ];
        
        $this->renderWithLayout('home/index', $data);
    }
    
    public function about()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Test flash message - trigger with ?test=flash
        if (isset($_GET['test']) && $_GET['test'] === 'flash') {
            $this->redirectWithMessage('/about', 'This is a test flash message!', 'success');
        }
        
        // Get flash message if any
        $flashMessage = $this->getFlashMessage();
        
        // Load contributors data
        require_once dirname(__DIR__) . "/components/contributor-list.php";
        
        $data = [
            'flashMessage' => $flashMessage,
            'title' => 'About Us - Art Gallery',
            'contributors' => $contributors
        ];
        
        $this->renderWithLayout('home/about', $data);
    }
}
