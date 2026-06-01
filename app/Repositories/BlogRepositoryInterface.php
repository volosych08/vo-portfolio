<?php

namespace App\Repositories;

use App\Models\BlogPost;

interface BlogRepositoryInterface
{
    /** @return BlogPost[] */
    public function all(): array;
    public function findById(int $id): ?BlogPost;
    /** @return BlogPost[] */
    public function findPublished(): array;
    public function findPublishedBySlug(string $slug): ?BlogPost;
    public function insert(BlogPost $post): BlogPost;
    public function update(BlogPost $post): ?BlogPost;
    public function delete(BlogPost $post): bool;
}
