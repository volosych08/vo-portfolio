<?php

namespace App\Repositories;

use Framework\Database;
use App\Models\ProfileSection;

class ProfileRepository implements ProfileRepositoryInterface
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function all(): array
    {
        $stmt = $this->database->run("SELECT * FROM profile_sections ORDER BY id")->fetchAll();

        $profiles = [];

        foreach ($stmt as $row) {
            $profiles[] = $this->fromDbRow($row);
        }

        return $profiles;
    }

    public function findBySectionKey(string $key): ?ProfileSection
    {
        $stmt = $this->database->run("SELECT * FROM profile_sections WHERE section_key = :sectionKey", [
            'sectionKey' => $key
        ])->fetch();

        if (!$stmt) {
            return  null;
        }

        return $this->fromDbRow($stmt);
    }

    public function update(ProfileSection $profileSection): ProfileSection
    {
        $this->database->run("UPDATE profile_sections SET 
                            title = :title,
                            content = :content,
                            updated_at = :updatedAt
                            WHERE id = :id", [
                                'id' => $profileSection->id,
                                'title' => $profileSection->title,
                                'content' => $profileSection->content,
                                'updatedAt' => $profileSection->updatedAt
        ]);

        return $profileSection;
    }

    private function fromDbRow(mixed $row): ProfileSection
    {
        $profileSection = new ProfileSection();

        $profileSection->id = $row->id;
        $profileSection->sectionKey = $row->section_key;
        $profileSection->title = $row->title;
        $profileSection->content = $row->content;
        $profileSection->updatedAt = $row->updated_at;

        return $profileSection;
    }
}
