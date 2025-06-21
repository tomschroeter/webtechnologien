<?php

require_once __DIR__ . "/BaseController.php";
require_once dirname(__DIR__) . "/repositories/SubjectRepository.php";
require_once dirname(__DIR__) . "/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/Database.php";

/**
 * Handles the listing and detail views of subjects.
 */
class SubjectController extends BaseController
{
    private Database $db;
    private SubjectRepository $subjectRepository;
    private ArtworkRepository $artworkRepository;

    /**
     * Initializes the database connection and repositories for subjects and artworks.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->subjectRepository = new SubjectRepository($this->db);
        $this->artworkRepository = new ArtworkRepository($this->db);
    }

    /**
     * Displays a list of all subjects.
     * Starts session if not started, fetches all subjects from the repository,
     * and renders the subjects index page.
     */
    public function index(): void
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

    /**
     * Displays details of a specific subject and its associated artworks.
     * 
     * @param int|string $id The ID of the subject to display.
     * 
     * @throws HttpException if the subject ID is invalid, not found, or if a database error occurs.
     */
    public function show($id): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Validate subject ID parameter
        if (!$id || !is_numeric($id)) {
            throw new HttpException(400, "The subject ID parameter is invalid or missing.");
        }

        $subjectId = (int) $id;

        try {
            // Fetch the subject by ID
            $subject = $this->subjectRepository->getSubjectById($subjectId);

            if (!$subject) {
                throw new HttpException(404, "No subject with the given ID was found.");
            }

            // Fetch artworks related to the subject
            $artworks = $this->artworkRepository->getArtworksBySubject($subjectId);

            $data = [
                'subject' => $subject,
                'artworks' => $artworks,
                'title' => $subject->getSubjectName() . ' - Subjects'
            ];

            // Render the subject detail page
            $this->renderWithLayout('subjects/show', $data);

        } catch (Exception $e) {
            error_log("Error loading subject: " . $e->getMessage());
            throw new HttpException(500, "A database error occurred while loading the subject. Please try again later.");
        }
    }
}
