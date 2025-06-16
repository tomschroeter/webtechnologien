<?php

require_once dirname(__DIR__) . "/controllers/BaseController.php";
require_once dirname(__DIR__) . "/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/repositories/ArtistRepository.php";
require_once dirname(__DIR__) . "/repositories/ReviewRepository.php";
require_once dirname(__DIR__) . "/Database.php";
require_once dirname(__DIR__) . "/components/find-image-ref.php";

class ArtworkController extends BaseController
{
    private $db;
    private $artworkRepository;
    private $artistRepository;
    private $reviewRepository;
    
    public function __construct()
    {
        $this->db = new Database();
        $this->artworkRepository = new ArtworkRepository($this->db);
        $this->artistRepository = new ArtistRepository($this->db);
        $this->reviewRepository = new ReviewRepository($this->db);
    }
    
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Get sort parameters from URL
        $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'title';
        $sortOrder = isset($_GET['order']) ? $_GET['order'] : 'asc';
        
        // Validate sort parameters
        $validSortFields = ['title', 'artist', 'year'];
        if (!in_array($sortBy, $validSortFields)) {
            $sortBy = 'title';
        }
        
        $validSortOrders = ['asc', 'desc'];
        if (!in_array($sortOrder, $validSortOrders)) {
            $sortOrder = 'asc';
        }
        
        // Get artworks with the sort parameters
        if ($sortBy === 'artist') {
            // For artist sorting, we need to use a different approach
            $artworks = $this->artworkRepository->getAllArtworks($sortBy, $sortOrder);
            
            // Add artist names to artworks
            foreach ($artworks as $artwork) {
                try {
                    $artist = $this->artistRepository->getArtistById($artwork->getArtistID());
                    $artwork->artistName = $artist->getFirstName() . ' ' . $artist->getLastName();
                } catch (Exception $e) {
                    $artwork->artistName = 'Unknown Artist';
                }
            }
        } else {
            $artworks = $this->artworkRepository->getAllArtworks($sortBy, $sortOrder);
            
            // Add artist names to all artworks
            foreach ($artworks as $artwork) {
                try {
                    $artist = $this->artistRepository->getArtistById($artwork->getArtistID());
                    $artwork->artistName = $artist->getFirstName() . ' ' . $artist->getLastName();
                } catch (Exception $e) {
                    $artwork->artistName = 'Unknown Artist';
                }
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
    
    public function show($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if artwork ID is provided and valid
        if (!$id || !is_numeric($id)) {
            $this->redirect("/error.php?error=invalidParam");
        }
        
        $artworkId = (int)$id;
        
        try {
            $artwork = $this->artworkRepository->findById($artworkId);
            $artist = $this->artistRepository->getArtistById($artwork->getArtistId());
            $reviews = $this->reviewRepository->getAllReviewsWithCustomerInfo($artworkId);
            
            if (!$artwork) {
                $this->redirect("/error.php?error=artworkNotFound");
            }
            
            $data = [
                'artwork' => $artwork,
                'artist' => $artist,
                'reviews' => $reviews,
                'title' => $artwork->getTitle() . ' - Artworks'
            ];
            
            $this->renderWithLayout('artworks/show', $data);
            
        } catch (Exception $e) {
            error_log("Error loading artwork: " . $e->getMessage());
            $this->redirect("/error.php?error=databaseError");
        }
    }
}
