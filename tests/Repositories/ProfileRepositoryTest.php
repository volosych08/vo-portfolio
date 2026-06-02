<?php

namespace Repositories;

use App\Repositories\ProfileRepository;
use Framework\Database;
use PHPUnit\Framework\TestCase;

class ProfileRepositoryTest extends TestCase
{
    private ProfileRepository $repository;

    protected function setUp(): void
    {
        $database = $this->createTestDatabase();

        $database->run("DROP TABLE IF EXISTS profile_sections");

        $database->run("
            CREATE TABLE profile_sections (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                section_key TEXT NOT NULL UNIQUE,
                title TEXT NOT NULL,
                content TEXT NOT NULL,
                updated_at TEXT NOT NULL
            )
        ");

        $database->run("
            INSERT INTO profile_sections (
                section_key,
                title,
                content,
                updated_at
            ) VALUES
            (
                'about_me',
                'About me',
                'Original about me content',
                '2026-06-01 10:00:00'
            ),
            (
                'programming_skills',
                'Programming',
                'HTML (Good)
CSS (Good)
PHP (Good)',
                '2026-06-01 10:00:00'
            ),
            (
                'languages',
                'Languages',
                'Ukrainian (Native)
English (B2)',
                '2026-06-01 10:00:00'
            )
        ");

        $this->repository = new ProfileRepository($database);
    }

    public function testAllReturnsAllProfileSections(): void
    {
        $sections = $this->repository->all();

        $this->assertCount(3, $sections);

        $sectionKeys = array_map(
            fn ($section): string => $section->sectionKey,
            $sections
        );

        $this->assertContains('about_me', $sectionKeys);
        $this->assertContains('programming_skills', $sectionKeys);
        $this->assertContains('languages', $sectionKeys);
    }

    public function testFindBySectionKeyReturnsProfileSection(): void
    {
        $section = $this->repository->findBySectionKey('about_me');

        $this->assertNotNull($section);
        $this->assertSame('about_me', $section->sectionKey);
        $this->assertSame('About me', $section->title);
        $this->assertSame('Original about me content', $section->content);
    }

    public function testFindBySectionKeyReturnsNullWhenSectionDoesNotExist(): void
    {
        $section = $this->repository->findBySectionKey('missing_section');

        $this->assertNull($section);
    }

    public function testUpdateChangesProfileSection(): void
    {
        $section = $this->repository->findBySectionKey('about_me');

        $this->assertNotNull($section);

        $section->title = 'Updated About Me';
        $section->content = 'Updated biography content';
        $section->updatedAt = '2026-06-02 12:00:00';

        $updatedSection = $this->repository->update($section);

        $this->assertSame('Updated About Me', $updatedSection->title);

        $foundSection = $this->repository->findBySectionKey('about_me');

        $this->assertNotNull($foundSection);
        $this->assertSame('Updated About Me', $foundSection->title);
        $this->assertSame('Updated biography content', $foundSection->content);
        $this->assertSame('about_me', $foundSection->sectionKey);
    }

    private function createTestDatabase(): Database
    {
        return new Database('sqlite::memory:');
    }
}
