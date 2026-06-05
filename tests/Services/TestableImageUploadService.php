<?php

namespace Services;

use App\Services\ImageUploadService;

class TestableImageUploadService extends ImageUploadService
{
    protected function moveUploadedFile(string $from, string $to): bool
    {
        return copy($from, $to);
    }
}