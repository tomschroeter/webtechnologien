<?php

require_once __DIR__ . "/BaseController.php";
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
        
        // Check for regular search query
        $searchQuery = trim($_GET['searchQuery'] ?? '');
        
        // Check for advanced search parameters
        $filterBy = $_GET['filterBy'] ?? '';
        $artistName = trim($_GET['artistName'] ?? '');
        $artworkTitle = trim($_GET['artworkTitle'] ?? '');
        $artistNationality = $_GET['artistNationality'] ?? '';
        $artworkGenre = $_GET['artworkGenre'] ?? '';
        $artistStartDate = $_GET['artistStartDate'] ?? '';
        $artistEndDate = $_GET['artistEndDate'] ?? '';
        $artworkStartDate = $_GET['artworkStartDate'] ?? '';
        $artworkEndDate = $_GET['artworkEndDate'] ?? '';
        
        // Determine if this is an advanced search or regular search
        $isAdvancedSearch = !empty($filterBy) || !empty($artistName) || !empty($artworkTitle) || 
                           !empty($artistNationality) || !empty($artworkGenre) ||
                           !empty($artistStartDate) || !empty($artistEndDate) ||
                           !empty($artworkStartDate) || !empty($artworkEndDate);
        
        // For regular search, require searchQuery
        if (!$isAdvancedSearch && empty($searchQuery)) {
            $this->redirectWithNotification(
                '/',
                'Please provide a search term.',
                'error'
            );
            return;
        }
        
        // For regular search, check if search query has valid size (>= 3 characters)
        if (!$isAdvancedSearch && strlen($searchQuery) < 3) {
            $this->redirectWithNotification(
                '/',
                'Your search query is too short. At least 3 characters are required.',
                'error'
            );
            return;
        }
        
        // Get sorting parameters
        $sortParameter = $_GET['sortParameter'] ?? 'Title'; // search by title by default
        $sortArtist = isset($_GET['sortArtist']) && $_GET['sortArtist'] === 'descending';
        $sortArtwork = isset($_GET['sortArtwork']) && $_GET['sortArtwork'] === 'descending';
        
        try {
            if ($isAdvancedSearch) {
                // Handle advanced search
                require_once dirname(__DIR__) . "/repositories/GenreRepository.php";
                $genreRepository = new GenreRepository($this->db);
                
                if ($filterBy === 'artist' || empty($filterBy)) {
                    // Search for artists with advanced criteria
                    $artistSearchResults = $this->artistRepository->getArtistByAdvancedSearch(
                        $artistName, $artistStartDate, $artistEndDate, $artistNationality, $sortArtist
                    );
                } else {
                    $artistSearchResults = [];
                }
                
                if ($filterBy === 'artwork' || empty($filterBy)) {
                    // Search for artworks with advanced criteria
                    $artworkSearchResults = $this->artworkRepository->getArtworksByAdvancedSearch(
                        $artworkTitle, $artworkStartDate, $artworkEndDate, $artworkGenre, $sortParameter, $sortArtwork
                    );
                } else {
                    $artworkSearchResults = [];
                }
                
                $searchQuery = $isAdvancedSearch ? '' : $searchQuery;
                $searchDisplayText = $isAdvancedSearch ? 'Advanced Search' : $searchQuery;
            } else {
                // Handle regular search
                $artistSearchResults = $this->artistRepository->getArtistBySearchQuery($searchQuery, $sortArtist);
                $artworkSearchResults = $this->artworkRepository->getArtworkBySearchQuery($searchQuery, $sortParameter, $sortArtwork);
                $searchDisplayText = $searchQuery;
            }
            
            $data = [
                'searchQuery' => $searchQuery,
                'searchDisplayText' => $searchDisplayText,
                'artistSearchResults' => $artistSearchResults,
                'artworkSearchResults' => $artworkSearchResults,
                'sortParameter' => $sortParameter,
                'sortArtist' => $sortArtist,
                'sortArtwork' => $sortArtwork,
                'isAdvancedSearch' => $isAdvancedSearch,
                'filterBy' => $filterBy ?? '',
                'artistName' => $artistName,
                'artistStartDate' => $artistStartDate,
                'artistEndDate' => $artistEndDate,
                'artworkStartDate' => $artworkStartDate,
                'artworkEndDate' => $artworkEndDate,
                'artworkTitle' => $artworkTitle,
                'artistNationality' => $artistNationality,
                'artworkGenre' => $artworkGenre,
                'title' => 'Search Results - Art Gallery'
            ];
            
            echo $this->renderWithLayout('search/index', $data);
            
        } catch (Exception $e) {
            $this->redirectWithNotification(
                '/',
                'Sorry something went wrong. Please try again.',
                'error',
            );
        }
    }
    
    public function advancedSearch()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        require_once dirname(__DIR__) . "/repositories/GenreRepository.php";
        $genreRepository = new GenreRepository($this->db);
        
        try {
            // Get data for form dropdowns
            $nationalities = $this->artistRepository->getArtistNationalities();
            $genres = $genreRepository->getAllGenres();
            $genreNames = [];
            foreach ($genres as $genre) {
                $genreNames[] = $genre->getGenreName();
            }
            
            // Get filter parameters
            $filterBy = $_GET['filterBy'] ?? 'artist';
            $selectedArtistNationality = $_GET['artistNationality'] ?? '';
            $selectedArtworkGenre = $_GET['artworkGenre'] ?? '';
            
            $data = [
                'nationalities' => $nationalities,
                'genreNames' => $genreNames,
                'filterBy' => $filterBy,
                'selectedArtistNationality' => $selectedArtistNationality,
                'selectedArtworkGenre' => $selectedArtworkGenre,
                'title' => 'Advanced Search - Art Gallery'
            ];
            
            echo $this->renderWithLayout('search/advanced', $data);
            
        } catch (Exception $e) {
            $this->redirectWithNotification(
                '/',
                'Sorry something went wrong. Please try again.',
                'error',
            );
        }
    }
}
