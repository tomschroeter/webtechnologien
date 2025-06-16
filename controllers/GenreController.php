<?php

require_once __DIR__ . "/BaseController.php";
require_once dirname(__DIR__) . "/repositories/GenreRepository.php";
require_once dirname(__DIR__) . "/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/Database.php";

class GenreController extends BaseController
{
    private $db;
    private $genreRepository;
    private $artworkRepository;
    
    public function __construct()
    {
        $this->db = new Database();
        $this->genreRepository = new GenreRepository($this->db);
        $this->artworkRepository = new ArtworkRepository($this->db);
    }
    
    public function index()
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
    
    public function show($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if genre ID is provided and valid
        if (!$id || !is_numeric($id)) {
            $this->redirect("/error.php?error=invalidParam");
        }
        
        $genreId = (int)$id;
        
        try {
            $genre = $this->genreRepository->getGenreById($genreId);
            $artworks = $this->artworkRepository->getArtworksByGenre($genreId);
            
            if (!$genre) {
                $this->redirect("/error.php?error=genreNotFound");
            }
            
            $data = [
                'genre' => $genre,
                'artworks' => $artworks,
                'title' => $genre->getGenreName() . ' - Genres'
            ];
            
            $this->renderWithLayout('genres/show', $data);
            
        } catch (Exception $e) {
            error_log("Error loading genre: " . $e->getMessage());
            $this->redirect("/error.php?error=databaseError");
        }
    }
}
