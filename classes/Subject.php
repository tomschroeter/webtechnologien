<?php

class Subject
{
    private $subjectId;
    private $subjectName;


    public function __construct($subjectName, $subjectId = null)
    {
        $this->subjectName = $subjectName;
        $this->subjectId = $subjectId;
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
