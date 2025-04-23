<?php

class Subject
{
    private $subjectId;
    private $subjectName;


    public function __construct($subjectName, $subjectId = null)
    {
        $this->setSubjectName($subjectName);
        $this->setSubjectId($subjectId);
    }

    public static function createSubjectFromRecord(array $record) : Subject {
        return new self(
            subjectName: $record['SubjectName'],
            subjectId: $record['SubjectId'],
        );
    }

    public function getSubjectId()
    {
        return $this->subjectId;
    }


    public function setSubjectId($subjectId)
    {
        $this->subjectId = $subjectId;
    }


    public function getSubjectName()
    {
        return $this->subjectName;
    }


    public function setSubjectName($subjectName)
    {
        $this->subjectName = $subjectName;
    }
}
