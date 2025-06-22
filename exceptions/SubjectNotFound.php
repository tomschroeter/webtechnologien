<?php

/**
 * Custom exception class for when no subject with the given ID was found.
 */
class SubjectNotFoundException extends Exception
{
    private int $subjectId;

    public function __construct(int $subjectId)
    {
        $this->subjectId = $subjectId;
 
        $message = "Subject with ID {$subjectId} couldn't be found.";
        parent::__construct($message);
    }

    public function getSubjectId(): int
    {
        return $this->subjectId;
    }
}
