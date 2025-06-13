<?php

require_once dirname(__DIR__) . "/controllers/BaseController.php";
require_once dirname(__DIR__) . "/repositories/ArtistRepository.php";
require_once dirname(__DIR__) . "/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/Database.php";
require_once dirname(__DIR__) . "/components/find_image_ref.php";

class ArtistController extends BaseController
{
    private $db;
    private $artistRepository;
    private $artworkRepository;
    
    public function __construct()
    {
        $this->db = new Database();
        $this->artistRepository = new ArtistRepository($this->db);
        $this->artworkRepository = new ArtworkRepository($this->db);
    }
    
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user has submitted a valid option for changing the display order
        $sort = isset($_GET['sort']) && $_GET['sort'] === 'descending';
        
        $artists = $this->artistRepository->getAllArtists($sort);
        
        $data = [
            'artists' => $artists,
            'sort' => $sort,
            'title' => 'Artists - Art Gallery'
        ];
        
        echo $this->renderWithLayout('artists/index', $data);
    }
    
    public function show($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if artist ID is provided and valid
        if (!$id || !is_numeric($id)) {
            $this->redirect("/error.php?error=invalidParam");
        }
        
        $artistId = (int)$id;
        
        // Load artist and artworks
        try {
            $artist = $this->artistRepository->getArtistById($artistId);
            $artworks = $this->artworkRepository->getArtworksByArtist($artistId);
            
            if (!$artist) {
                $this->redirect("/error.php?error=artistNotFound");
            }
            
            $data = [
                'artist' => $artist,
                'artworks' => $artworks,
                'title' => $artist->getFirstName() . ' ' . $artist->getLastName() . ' - Artists'
            ];
            
            echo $this->renderWithLayout('artists/show', $data);
            
        } catch (Exception $e) {
            error_log("Error loading artist: " . $e->getMessage());
            $this->redirect("/error.php?error=databaseError");
        }
    }
}
