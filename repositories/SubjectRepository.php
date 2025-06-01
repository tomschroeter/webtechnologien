<?php

require_once dirname(__DIR__)."/Database.php";
require_once dirname(__DIR__)."/classes/Subject.php";

class SubjectRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @return Subject[]
     */
    public function getAllSubjects(): array
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "SELECT * FROM subjects ORDER BY SubjectName ASC";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->execute();

        $subjects = [];

        foreach ($stmt as $row) {
            $subjects[] = Subject::createSubjectFromRecord($row);
        }

        $this->db->disconnect();

        return $subjects;
    }

    /**
    * @throws Exception if subject couldn't be found
    */
    public function getSubjectById(int $subjectId): Subject
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "SELECT * FROM subjects WHERE SubjectId = :id";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue("id", $subjectId, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();

        $this->db->disconnect();

        if ($row !== false) {
            return Subject::createSubjectFromRecord($row);
        } else {
            throw new Exception("Subject with ID {$subjectId} couldn't be found");
        }
    }

    /**
     * Get subjects for a specific artwork
     * @param int $artworkId
     * @return Subject[]
     */
    public function getSubjectsByArtwork(int $artworkId): array
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "
            SELECT s.*
            FROM subjects s
            JOIN artworksubjects ars ON s.SubjectID = ars.SubjectID
            WHERE ars.ArtworkID = :artworkId
            ORDER BY s.SubjectName ASC
        ";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue("artworkId", $artworkId, PDO::PARAM_INT);
        $stmt->execute();

        $subjects = [];
        foreach ($stmt as $row) {
            $subjects[] = Subject::createSubjectFromRecord($row);
        }

        $this->db->disconnect();
        return $subjects;
    }
}
