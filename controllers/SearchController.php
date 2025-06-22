<?php

require_once __DIR__ . "/BaseController.php";
require_once dirname(__DIR__) . "/repositories/ArtistRepository.php";
require_once dirname(__DIR__) . "/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/Database.php";
require_once dirname(__DIR__) . "/exceptions/HttpException.php";

/**
 * Handles display of (advanced) search results.
 */
class SearchController extends BaseController
{
    private Database $db;
    private ArtistRepository $artistRepository;
    private ArtworkRepository $artworkRepository;

    /**
     * Initializes the database connection and repositories for artists and artworks.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->artistRepository = new ArtistRepository($this->db);
        $this->artworkRepository = new ArtworkRepository($this->db);
    }

    /**
     * Handles search requests for artists and artworks.
     * Supports both regular search and advanced search with multiple filters.
     * Renders the search results page or redirects with error notifications.
     */
    public function search(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Get search parameters from GET request
        $searchQuery = trim($_GET['searchQuery'] ?? '');
        $filterBy = $_GET['filterBy'] ?? '';
        $artistName = trim($_GET['artistName'] ?? '');
        $artworkTitle = trim($_GET['artworkTitle'] ?? '');
        $artistNationality = $_GET['artistNationality'] ?? '';
        $artworkGenre = $_GET['artworkGenre'] ?? '';
        $artistStartDate = $_GET['artistStartDate'] ?? '';
        $artistEndDate = $_GET['artistEndDate'] ?? '';
        $artworkStartDate = $_GET['artworkStartDate'] ?? '';
        $artworkEndDate = $_GET['artworkEndDate'] ?? '';

        // Determine if the search is advanced based on presence of advanced filters
        $isAdvancedSearch = !empty($filterBy) || !empty($artistName) || !empty($artworkTitle) ||
            !empty($artistNationality) || !empty($artworkGenre) ||
            !empty($artistStartDate) || !empty($artistEndDate) ||
            !empty($artworkStartDate) || !empty($artworkEndDate);

        // For regular search, validate that searchQuery is provided and sufficiently long
        if (!$isAdvancedSearch && empty($searchQuery)) {
            $this->redirectWithNotification(
                '/',
                'Please provide a search term.',
                'error'
            );
            return;
        }

        if (!$isAdvancedSearch && strlen($searchQuery) < 3) {
            $this->redirectWithNotification(
                '/',
                'Your search query is too short. At least 3 characters are required.',
                'error'
            );
            return;
        }

        // Retrieve sorting preferences from GET parameters
        $sortParameter = $_GET['sortParameter'] ?? 'Title'; // Default sort by Title
        $sortArtist = isset($_GET['sortArtist']) && $_GET['sortArtist'] === 'descending';
        $sortArtwork = isset($_GET['sortArtwork']) && $_GET['sortArtwork'] === 'descending';

        if ($isAdvancedSearch) {
            // Load GenreRepository for filtering by genre
            require_once dirname(__DIR__) . "/repositories/GenreRepository.php";
            $genreRepository = new GenreRepository($this->db);

            // Advanced search for artists if filterBy is 'artist' or empty
            if ($filterBy === 'artist' || empty($filterBy)) {
                $artistSearchResults = $this->artistRepository->getArtistByAdvancedSearch(
                    $artistName,
                    $artistStartDate,
                    $artistEndDate,
                    $artistNationality,
                    $sortArtist
                );
            } else {
                $artistSearchResults = [];
            }

            // Advanced search for artworks if filterBy is 'artwork' or empty
            if ($filterBy === 'artwork' || empty($filterBy)) {
                $artworkSearchResults = $this->artworkRepository->getArtworksByAdvancedSearch(
                    $artworkTitle,
                    $artworkStartDate,
                    $artworkEndDate,
                    $artworkGenre,
                    $sortParameter,
                    $sortArtwork
                );
            } else {
                $artworkSearchResults = [];
            }

            $searchQuery = $isAdvancedSearch ? '' : $searchQuery;
            $searchDisplayText = $isAdvancedSearch ? 'Advanced Search' : $searchQuery;
        } else {
            // Regular search for artists and artworks by search query
            $artistSearchResults = $this->artistRepository->getArtistBySearchQuery($searchQuery, $sortArtist);
            $artworkSearchResults = $this->artworkRepository->getArtworkBySearchQuery($searchQuery, $sortParameter, $sortArtwork);
            $searchDisplayText = $searchQuery;
        }

        // Prepare data array for view rendering
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

        // Render the search results view
        echo $this->renderWithLayout('search/index', $data);
    }

    /**
     * Renders the advanced search form page.
     * Loads dropdown data such as nationalities and genres for filters.
     */
    public function advancedSearch(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require_once dirname(__DIR__) . "/repositories/GenreRepository.php";
        $genreRepository = new GenreRepository($this->db);

        // Retrieve list of artist nationalities for filter dropdown
        $nationalities = $this->artistRepository->getArtistNationalities();

        // Retrieve list of genres for filter dropdown
        $genres = $genreRepository->getAllGenres();
        $genreNames = [];
        foreach ($genres as $genre) {
            $genreNames[] = $genre->getGenreName();
        }

        // Retrieve currently selected filters from GET parameters
        $filterBy = $_GET['filterBy'] ?? 'artist';
        $selectedArtistNationality = $_GET['artistNationality'] ?? '';
        $selectedArtworkGenre = $_GET['artworkGenre'] ?? '';

        // Prepare data array for view rendering
        $data = [
            'nationalities' => $nationalities,
            'genreNames' => $genreNames,
            'filterBy' => $filterBy,
            'selectedArtistNationality' => $selectedArtistNationality,
            'selectedArtworkGenre' => $selectedArtworkGenre,
            'title' => 'Advanced Search - Art Gallery'
        ];

        // Render advanced search form view
        echo $this->renderWithLayout('search/advanced', $data);
    }
}
