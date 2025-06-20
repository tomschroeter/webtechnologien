<?php

require_once __DIR__ . "/BaseController.php";
require_once dirname(__DIR__) . "/repositories/SubjectRepository.php";
require_once dirname(__DIR__) . "/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/Database.php";

class SubjectController extends BaseController
{
    private $db;
    private $subjectRepository;
    private $artworkRepository;
    
    public function __construct()
    {
        $this->db = new Database();
        $this->subjectRepository = new SubjectRepository($this->db);
        $this->artworkRepository = new ArtworkRepository($this->db);
    }
    
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $subjects = $this->subjectRepository->getAllSubjects();
        
        $data = [
            'subjects' => $subjects,
            'title' => 'Subjects - Art Gallery'
        ];
        
        $this->renderWithLayout('subjects/index', $data);
    }
    
    public function show($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if subject ID is provided and valid
        if (!$id || !is_numeric($id)) {
            throw new HttpException(400, "The subject ID parameter is invalid or missing.");
        }
        
        $subjectId = (int)$id;
        
        try {
            $subject = $this->subjectRepository->getSubjectById($subjectId);
            
            if (!$subject) {
                throw new HttpException(404, "No subject with the given ID was found.");
            }
            
            $artworks = $this->artworkRepository->getArtworksBySubject($subjectId);
            
            $data = [
                'subject' => $subject,
                'artworks' => $artworks,
                'title' => $subject->getSubjectName() . ' - Subjects'
            ];
            
            $this->renderWithLayout('subjects/show', $data);
            
        } catch (Exception $e) {
            error_log("Error loading subject: " . $e->getMessage());
            throw new HttpException(500, "A database error occurred while loading the subject. Please try again later.");
        }
    }
}
