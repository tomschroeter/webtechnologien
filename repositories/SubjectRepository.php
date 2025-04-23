<?php

require_once dirname(__DIR__)."/Database.php";
require_once dirname(__DIR__)."/classes/Subject.php";

class SubjectRepository {
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    /**
     * @return Subject[]
    */
    public function getAllSubjects() : array
    {
        $this->db->connect();

        $sql = "SELECT * FROM subjects ORDER BY SubjectName ASC";
        
        $stmt = $this->db->prepareStatement($sql);
        $stmt->execute();

        $subjects = [];

        foreach ($stmt as $row)
        {
            $subjects[] = Subject::createSubjectFromRecord($row);
        }

        $this->db->disconnect();

        return $subjects;
    }
}