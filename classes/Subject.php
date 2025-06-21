<?php

/**
 * Represents a subject entity from the database.
 * 
 * This class encapsulates artwork data and provides
 * getter and setter methods for accessing and modifying
 * subject properties.
 * 
 * Instances are created using the static method `createSubjectFromRecord()`,
 * which accepts an associative array (e.g., a database record).
 */
class Subject
{
    private int $subjectId;
    private string $subjectName;

    private function __construct(string $subjectName, int $subjectId)
    {
        $this->setSubjectName($subjectName);
        $this->setSubjectId($subjectId);
    }

    public static function createSubjectFromRecord(array $record): Subject
    {
        return new self(
            $record['SubjectName'],
            (int) $record['SubjectId']
        );
    }

    public function getSubjectId(): int
    {
        return $this->subjectId;
    }

    public function setSubjectId(int $subjectId): void
    {
        $this->subjectId = $subjectId;
    }

    public function getSubjectName(): string
    {
        return $this->subjectName;
    }

    public function setSubjectName(string $subjectName): void
    {
        $this->subjectName = $subjectName;
    }
}
