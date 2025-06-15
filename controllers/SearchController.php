<?php

require_once dirname(__DIR__) . "/controllers/BaseController.php";
require_once dirname(__DIR__) . "/repositories/ArtistRepository.php";
require_once dirname(__DIR__) . "/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/Database.php";

class SearchController extends BaseController
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
    
    public function search()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if search query has been submitted
        $searchQuery = trim($_GET['searchQuery'] ?? '');
        
        if (empty($searchQuery)) {
            $this->redirect('/error.php?error=missingParam');
            return;
        }
        
        // Check if search query has valid size (>= 3 characters)
        if (strlen($searchQuery) < 3) {
            $this->redirect('/error.php?error=tooShort');
            return;
        }
        
        // Get sorting parameters
        $sortParameter = $_GET['sortParameter'] ?? 'Title'; // search by title by default
        $sortArtist = isset($_GET['sortArtist']) && $_GET['sortArtist'] === 'descending';
        $sortArtwork = isset($_GET['sortArtwork']) && $_GET['sortArtwork'] === 'descending';
        
        try {
            // Get results for all artists that fit the search query
            $artistSearchResults = $this->artistRepository->getArtistBySearchQuery($searchQuery, $sortArtist);
            
            // Get results for all artworks that fit the search query
            $artworkSearchResults = $this->artworkRepository->getArtworkBySearchQuery($searchQuery, $sortParameter, $sortArtwork);
            
            $data = [
                'searchQuery' => $searchQuery,
                'artistSearchResults' => $artistSearchResults,
                'artworkSearchResults' => $artworkSearchResults,
                'sortParameter' => $sortParameter,
                'sortArtist' => $sortArtist,
                'sortArtwork' => $sortArtwork,
                'title' => 'Search Results - Art Gallery'
            ];
            
            echo $this->renderWithLayout('search/index', $data);
            
        } catch (Exception $e) {
            $this->redirect('/error.php?error=searchError');
        }
    }
}
