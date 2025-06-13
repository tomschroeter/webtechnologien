<?php

require_once dirname(__DIR__) . "/controllers/BaseController.php";
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
            $this->redirect("/error.php?error=invalidParam");
        }
        
        $subjectId = (int)$id;
        
        try {
            $subject = $this->subjectRepository->getSubjectById($subjectId);
            $artworks = $this->artworkRepository->getArtworksBySubject($subjectId);
            
            if (!$subject) {
                $this->redirect("/error.php?error=subjectNotFound");
            }
            
            $data = [
                'subject' => $subject,
                'artworks' => $artworks,
                'title' => $subject->getSubjectName() . ' - Subjects'
            ];
            
            $this->renderWithLayout('subjects/show', $data);
            
        } catch (Exception $e) {
            error_log("Error loading subject: " . $e->getMessage());
            $this->redirect("/error.php?error=databaseError");
        }
    }
}
