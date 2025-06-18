<?php

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
