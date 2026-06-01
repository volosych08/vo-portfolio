<?php

namespace App\Models;

class BlogPost
{
    public int $id;

    public string $title;

    public string $slug;

    public string $excerpt;

    public string $content;

    public string $status = 'published';

    public string $cardImage;

    public string $heroImage;

    public string $publishedAt;

    public string $createdAt;

    public string $updatedAt;
}
