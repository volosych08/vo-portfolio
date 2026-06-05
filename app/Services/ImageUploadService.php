<?php

namespace App\Services;

class ImageUploadService
{
    /**
     * @throws \RuntimeException
     */
    public function uploadImage(
        string $fieldName,
        string $folder,
        ?string $currentPath = null
    ): ?string {
        if (!isset($_FILES[$fieldName])) {
            return $currentPath;
        }

        $file = $_FILES[$fieldName];

        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return $currentPath;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Image upload failed.');
        }

        $maxSize = 10 * 1024 * 1024; // 10 MB

        if ($file['size'] > $maxSize) {
            throw new \RuntimeException('Image must be smaller than 10 MB.');
        }

        $allowedMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        $mimeType = mime_content_type($file['tmp_name']);

        if (!isset($allowedMimeTypes[$mimeType])) {
            throw new \RuntimeException('Only JPG, PNG and WEBP images are allowed.');
        }

        $extension = $allowedMimeTypes[$mimeType];
        $safeFolder = trim($folder, '/');

        $uploadDir = __DIR__ . '/../../public/uploads/' . $safeFolder;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $fileName = uniqid($safeFolder . '_', true) . '.' . $extension;
        $targetPath = $uploadDir . '/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new \RuntimeException('Could not save uploaded image.');
        }

        return '/uploads/' . $safeFolder . '/' . $fileName;
    }
}
