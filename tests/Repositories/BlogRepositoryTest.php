<?php

namespace Repositories;

use App\Models\BlogPost;
use App\Repositories\BlogRepository;
use Framework\Database;
use PHPUnit\Framework\TestCase;

class BlogRepositoryTest extends TestCase
{
    private BlogRepository $repository;

    protected function setUp(): void
    {
        $database = $this->createTestDatabase();

        $database->run("DROP TABLE IF EXISTS blog_posts");

        $database->run("
            CREATE TABLE blog_posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                slug TEXT NOT NULL UNIQUE,
                excerpt TEXT NOT NULL,
                content TEXT NOT NULL,
                status TEXT NOT NULL DEFAULT 'draft',
                card_image TEXT NOT NULL,
                hero_image TEXT NOT NULL,
                published_at TEXT NOT NULL,
                created_at TEXT NOT NULL,
                updated_at TEXT NOT NULL
            )
        ");

        $database->run("
            INSERT INTO blog_posts (
                title,
                slug,
                excerpt,
                content,
                status,
                card_image,
                hero_image,
                published_at,
                created_at,
                updated_at
            ) VALUES
            (
                'Published Post',
                'published-post',
                'Published excerpt',
                'Published content',
                'published',
                '/img/published-card.jpg',
                '/img/published-hero.jpg',
                '2026-06-01',
                '2026-06-01 10:00:00',
                '2026-06-01 10:00:00'
            ),
            (
                'Draft Post',
                'draft-post',
                'Draft excerpt',
                'Draft content',
                'draft',
                '/img/draft-card.jpg',
                '/img/draft-hero.jpg',
                '2026-06-02',
                '2026-06-02 10:00:00',
                '2026-06-02 10:00:00'
            )
        ");

        $this->repository = new BlogRepository($database);
    }

    public function testAllReturnsAllBlogPosts(): void
    {
        $posts = $this->repository->all();

        $this->assertCount(2, $posts);
        $this->assertSame('Draft Post', $posts[0]->title);
        $this->assertSame('Published Post', $posts[1]->title);
    }

    public function testFindPublishedReturnsOnlyPublishedPosts(): void
    {
        $posts = $this->repository->findPublished();

        $this->assertCount(1, $posts);
        $this->assertSame('Published Post', $posts[0]->title);
        $this->assertSame('published', $posts[0]->status);
    }

    public function testFindBySlugReturnsDraftOrPublishedPost(): void
    {
        $post = $this->repository->findBySlug('draft-post');

        $this->assertNotNull($post);
        $this->assertSame('Draft Post', $post->title);
        $this->assertSame('draft', $post->status);
        $this->assertSame('/img/draft-card.jpg', $post->cardImage);
        $this->assertSame('/img/draft-hero.jpg', $post->heroImage);
    }

    public function testFindBySlugReturnsNullWhenPostDoesNotExist(): void
    {
        $post = $this->repository->findBySlug('missing-post');

        $this->assertNull($post);
    }

    public function testFindPublishedBySlugReturnsPublishedPost(): void
    {
        $post = $this->repository->findPublishedBySlug('published-post');

        $this->assertNotNull($post);
        $this->assertSame('Published Post', $post->title);
        $this->assertSame('published', $post->status);
    }

    public function testFindPublishedBySlugDoesNotReturnDraftPost(): void
    {
        $post = $this->repository->findPublishedBySlug('draft-post');

        $this->assertNull($post);
    }

    public function testInsertCreatesNewBlogPost(): void
    {
        $post = new BlogPost();
        $post->title = 'New Post';
        $post->slug = 'new-post';
        $post->excerpt = 'New excerpt';
        $post->content = 'New content';
        $post->status = 'published';
        $post->cardImage = '/img/new-card.jpg';
        $post->heroImage = '/img/new-hero.jpg';
        $post->publishedAt = '2026-06-03';
        $post->createdAt = '2026-06-03 10:00:00';
        $post->updatedAt = '2026-06-03 10:00:00';

        $insertedPost = $this->repository->insert($post);

        $this->assertGreaterThan(0, $insertedPost->id);

        $foundPost = $this->repository->findBySlug('new-post');

        $this->assertNotNull($foundPost);
        $this->assertSame('New Post', $foundPost->title);
        $this->assertSame('/img/new-card.jpg', $foundPost->cardImage);
    }

    public function testUpdateChangesExistingBlogPost(): void
    {
        $post = $this->repository->findBySlug('published-post');

        $this->assertNotNull($post);

        $post->title = 'Updated Post';
        $post->slug = 'updated-post';
        $post->excerpt = 'Updated excerpt';
        $post->content = 'Updated content';
        $post->status = 'draft';
        $post->cardImage = '/img/updated-card.jpg';
        $post->heroImage = '/img/updated-hero.jpg';
        $post->publishedAt = '2026-06-04';
        $post->updatedAt = '2026-06-04 10:00:00';

        $updatedPost = $this->repository->update($post);

        $this->assertSame('Updated Post', $updatedPost->title);

        $foundPost = $this->repository->findBySlug('updated-post');

        $this->assertNotNull($foundPost);
        $this->assertSame('Updated content', $foundPost->content);
        $this->assertSame('draft', $foundPost->status);
        $this->assertSame('/img/updated-hero.jpg', $foundPost->heroImage);
    }

    public function testDeleteRemovesBlogPost(): void
    {
        $post = $this->repository->findBySlug('draft-post');

        $this->assertNotNull($post);

        $deleted = $this->repository->delete($post);

        $this->assertTrue($deleted);
        $this->assertNull($this->repository->findBySlug('draft-post'));
        $this->assertCount(1, $this->repository->all());
    }

    private function createTestDatabase(): Database
    {
        return new Database('sqlite::memory:');
    }
}
