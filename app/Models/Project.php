<?php

namespace App\Models;

class Project
{
    public int $id;

    public string $title;

    public string $description;

    public string $longDescription;

    /** @var string[] */
    public array $tech = [];

    public string $status;

    public string $completedAt;

    public string $category;

    public string $image;

    public string $url;

    public int $sortOrder;

    public string $createdAt;

    public string $updatedAt;
}
