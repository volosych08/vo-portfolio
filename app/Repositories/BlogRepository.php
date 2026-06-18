<?php

namespace App\Repositories;

use App\Models\BlogPost;
use Framework\Database;

class BlogRepository implements BlogRepositoryInterface
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @return BlogPost[]
     */
    public function all(): array
    {
        $stmt = $this->database
            ->run("SELECT * FROM blog_posts ORDER BY published_at DESC")
            ->fetchAll();

        $posts = [];

        foreach ($stmt as $row) {
            $posts[] = $this->fromDbRow($row);
        }

        return $posts;
    }

    /**
     * @return BlogPost[]
     */
    public function findPublished(): array
    {
        $stmt = $this->database
            ->run(
                "SELECT * FROM blog_posts WHERE status = :status ORDER BY published_at DESC",
                ["status" => "published"]
            )
            ->fetchAll();

        $posts = [];

        foreach ($stmt as $row) {
            $posts[] = $this->fromDbRow($row);
        }

        return $posts;
    }

    public function findById(int $id): ?BlogPost
    {
        $stmt = $this->database
            ->run("SELECT * FROM blog_posts WHERE id = :id", ["id" => $id])
            ->fetch();

        if (!$stmt) {
            return null;
        }

        return $this->fromDbRow($stmt);
    }

    public function findPublishedBySlug(string $slug): ?BlogPost
    {
        $stmt = $this->database
            ->run(
                "SELECT * FROM blog_posts WHERE slug = :slug AND status = :status",
                [
                    "slug" => $slug,
                    "status" => "published",
                ]
            )
            ->fetch();

        if (!$stmt) {
            return null;
        }

        return $this->fromDbRow($stmt);
    }

    public function findBySlug(string $slug): ?BlogPost
    {
        $stmt = $this->database
            ->run("SELECT * FROM blog_posts WHERE slug = :slug", [
                "slug" => $slug,
            ])
            ->fetch();

        if (!$stmt) {
            return null;
        }

        return $this->fromDbRow($stmt);
    }

    public function insert(BlogPost $post): BlogPost
    {
        $this->database->run(
            "INSERT INTO blog_posts 
            (title, slug, excerpt, content, status, card_image, hero_image, published_at, created_at, updated_at) 
            VALUES 
            (:title, :slug, :excerpt, :content, :status, :card_image,
             :hero_image, :published_at, :created_at, :updated_at)",
            [
                "title" => $post->title,
                "slug" => $post->slug,
                "excerpt" => $post->excerpt,
                "content" => $post->content,
                "status" => $post->status,
                "card_image" => $post->cardImage,
                "hero_image" => $post->heroImage,
                "published_at" => $post->publishedAt,
                "created_at" => $post->createdAt,
                "updated_at" => $post->updatedAt,
            ]
        );

        $post->id = $this->database->getLastID();

        return $post;
    }

    public function update(BlogPost $post): BlogPost
    {
        $this->database->run(
            "UPDATE blog_posts 
             SET title = :title,
                 slug = :slug,
                 excerpt = :excerpt,
                 content = :content,
                 status = :status,
                 card_image = :card_image,
                 hero_image = :hero_image,
                 published_at = :published_at,
                 updated_at = :updated_at
             WHERE id = :id",
            [
                "id" => $post->id,
                "title" => $post->title,
                "slug" => $post->slug,
                "excerpt" => $post->excerpt,
                "content" => $post->content,
                "status" => $post->status,
                "card_image" => $post->cardImage,
                "hero_image" => $post->heroImage,
                "published_at" => $post->publishedAt,
                "updated_at" => $post->updatedAt,
            ]
        );

        return $post;
    }

    public function delete(BlogPost $post): bool
    {
        $stmt = $this->database->run(
            "DELETE FROM blog_posts WHERE id = :id",
            ["id" => $post->id]
        );

        return $stmt->rowCount() > 0;
    }

    private function fromDbRow(mixed $row): BlogPost
    {
        $post = new BlogPost();

        $post->id = $row->id;
        $post->title = $row->title;
        $post->slug = $row->slug;
        $post->excerpt = $row->excerpt;
        $post->content = $row->content;
        $post->status = $row->status;
        $post->cardImage = $row->card_image;
        $post->heroImage = $row->hero_image;
        $post->publishedAt = $row->published_at;
        $post->createdAt = $row->created_at;
        $post->updatedAt = $row->updated_at;

        return $post;
    }
}
