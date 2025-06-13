<?php

require_once dirname(__DIR__) . "/controllers/BaseController.php";
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
        
        // Get flash message if any
        $flashMessage = $this->getFlashMessage();
        
        $data = [
            'flashMessage' => $flashMessage,
            'title' => 'About Us - Art Gallery'
        ];
        
        $this->renderWithLayout('home/about', $data);
    }
}
