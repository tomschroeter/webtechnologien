<?php

require_once dirname(__DIR__)."/Database.php";
require_once dirname(__DIR__)."/classes/Subject.php";

class SubjectRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * @return Subject[]
    */
    public function getAllSubjects() : array {

        $sql = "SELECT * FROM subjects ORDER BY SubjectName ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $subjects = [];
        foreach ($stmt as $row) {
            $subjects[] = Subject::createSubjectFromRecord($row);
        }

        return $subjects;
    }
}