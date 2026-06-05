<?php

namespace Services;

use App\Services\ImageUploadService;
use PHPUnit\Framework\TestCase;

class ImageUploadServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        $_FILES = [];

        $uploadDirectory = __DIR__ . '/../../public/uploads/test_projects';

        if (!is_dir($uploadDirectory)) {
            return;
        }

        foreach (glob($uploadDirectory . '/*') as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        rmdir($uploadDirectory);
    }

    public function testReturnsCurrentPathWhenNoFileWasUploaded(): void
    {
        $service = new TestableImageUploadService();

        $result = $service->uploadImage(
            'image_upload',
            'test_projects',
            '/uploads/projects/current.png'
        );

        $this->assertSame('/uploads/projects/current.png', $result);
    }

    public function testReturnsCurrentPathWhenUploadFieldHasNoFile(): void
    {
        $_FILES['image_upload'] = [
            'name' => '',
            'type' => '',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE,
            'size' => 0,
        ];

        $service = new TestableImageUploadService();

        $result = $service->uploadImage(
            'image_upload',
            'test_projects',
            '/uploads/projects/current.png'
        );

        $this->assertSame('/uploads/projects/current.png', $result);
    }

    public function testThrowsExceptionWhenFileIsTooLarge(): void
    {
        $_FILES['image_upload'] = [
            'name' => 'large.png',
            'type' => 'image/png',
            'tmp_name' => $this->createPngFile(),
            'error' => UPLOAD_ERR_OK,
            'size' => 11 * 1024 * 1024,
        ];

        $service = new TestableImageUploadService();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Image must be smaller than 10 MB.');

        $service->uploadImage('image_upload', 'test_projects');
    }

    public function testThrowsExceptionWhenMimeTypeIsNotAllowed(): void
    {
        $textFile = tempnam(sys_get_temp_dir(), 'upload_test_');
        file_put_contents($textFile, 'This is not an image.');

        $_FILES['image_upload'] = [
            'name' => 'file.txt',
            'type' => 'text/plain',
            'tmp_name' => $textFile,
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($textFile),
        ];

        $service = new TestableImageUploadService();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Only JPG, PNG and WEBP images are allowed.');

        $service->uploadImage('image_upload', 'test_projects');
    }

    public function testUploadsValidImage(): void
    {
        $pngFile = $this->createPngFile();

        $_FILES['image_upload'] = [
            'name' => 'image.png',
            'type' => 'image/png',
            'tmp_name' => $pngFile,
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($pngFile),
        ];

        $service = new TestableImageUploadService();

        $result = $service->uploadImage('image_upload', 'test_projects');

        $this->assertIsString($result);
        $this->assertStringStartsWith('/uploads/test_projects/test_projects_', $result);
        $this->assertStringEndsWith('.png', $result);

        $fullPath = __DIR__ . '/../../public' . $result;

        $this->assertFileExists($fullPath);
    }

    private function createPngFile(): string
    {
        $file = tempnam(sys_get_temp_dir(), 'upload_test_');

        $pngData = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII='
        );

        file_put_contents($file, $pngData);

        return $file;
    }
}
