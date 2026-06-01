<?php

namespace App\Repositories;

use App\Models\ProfileSection;

interface ProfileRepositoryInterface
{
    /** @return ProfileSection[] */
    public function all(): array;
    public function update(ProfileSection $profileSection): ProfileSection;
    public function findBySectionKey(string $key): ?ProfileSection;
}
