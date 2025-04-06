<?php

class Subject {
    private $subjectID;
    private $subjectName;

    // Constructor using individual parameters
    public function __construct($subjectName, $subjectID = null) {
        $this->subjectName = $subjectName;
        $this->subjectID = $subjectID;
    }

// Getter
public function getSubjectID() {
    return $this->subjectID;
}

// Setter
public function setSubjectID($subjectID) {
    $this->subjectID = $subjectID;
}

// Getter
public function getSubjectName() {
    return $this->subjectName;
}

// Setter
public function setSubjectName($subjectName) {
    $this->subjectName = $subjectName;
}


}
