<?php

namespace App\Controllers;

use App\Repositories\BlogRepositoryInterface;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class BlogController
{
    private ResponseFactory $responseFactory;
    private BlogRepositoryInterface $blogRepository;
    public function __construct(
        ResponseFactory $responseFactory,
        BlogRepositoryInterface $blogRepository
    ) {
        $this->responseFactory = $responseFactory;
        $this->blogRepository = $blogRepository;
    }

    public function index(Request $request): Response
    {
        $user = $request->getAttribute('user');

        if ($user && $user->role === 'admin') {
            $posts = $this->blogRepository->all();
        } else {
            $posts = $this->blogRepository->findPublished();
        }

        return $this->responseFactory->view('blog/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    public function show(Request $request): Response
    {
        $slug = $request->routeParameters['slug'] ?? null;

        if ($slug === null) {
            return new Response('Blog post not found', 404);
        }

        $user = $request->getAttribute('user');

        if ($user && $user->role === 'admin') {
            $post = $this->blogRepository->findBySlug($slug);
        } else {
            $post = $this->blogRepository->findPublishedBySlug($slug);
        }

        if ($post === null) {
            return new Response('Blog post not found', 404);
        }

        return $this->responseFactory->view('blog/show.html.twig', [
            'post' => $post,
        ]);
    }

    public function edit(Request $request): Response
    {
        $slug = $request->routeParameters['slug'] ?? null;

        if ($slug === null) {
            return new Response('Blog post not found', 404);
        }

        $post = $this->blogRepository->findBySlug($slug);

        if ($post === null) {
            return new Response('Blog post not found', 404);
        }

        return $this->responseFactory->view('blog/edit.html.twig', [
            'post' => $post,
            'errors' => [],
        ]);
    }

    private function uploadBlogImage(string $fieldName, string $currentPath): string
    {
        if (!isset($_FILES[$fieldName])) {
            return $currentPath;
        }

        $file = $_FILES[$fieldName];

        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return $currentPath;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return $currentPath;
        }

        $maxSize = 5 * 1024 * 1024;

        if ($file['size'] > $maxSize) {
            return $currentPath;
        }

        $allowedMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        $mimeType = mime_content_type($file['tmp_name']);

        if (!isset($allowedMimeTypes[$mimeType])) {
            return $currentPath;
        }

        $extension = $allowedMimeTypes[$mimeType];
        $fileName = uniqid('blog_', true) . '.' . $extension;

        $uploadDir = __DIR__ . '/../../public/uploads/blog';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $targetPath = $uploadDir . '/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $currentPath;
        }

        return '/uploads/blog/' . $fileName;
    }

    public function update(Request $request): Response
    {
        $errors = $this->validate($request);
        $slug = $request->routeParameters['slug'] ?? null;

        if ($slug === null) {
            return new Response('Blog post not found', 404);
        }

        $post = $this->blogRepository->findBySlug($slug);

        if ($post === null) {
            return new Response('Blog post not found', 404);
        }

        if (!empty($errors)) {
            return $this->responseFactory->view('blog/edit.html.twig', [
                'post' => $post,
                'errors' => $errors,
            ]);
        }

        $post->title = trim($request->get('title') ?? '');
        $post->slug = trim($request->get('slug') ?? '');
        $post->excerpt = trim($request->get('excerpt') ?? '');
        $post->content = trim($request->get('content') ?? '');
        $post->status = trim($request->get('status') ?? 'draft');

        $post->cardImage = $this->uploadBlogImage('card_image_upload', $post->cardImage);
        $post->heroImage = $this->uploadBlogImage('hero_image_upload', $post->heroImage);

        $post->updatedAt = date('Y-m-d H:i:s');

        $this->blogRepository->update($post);

        return $this->responseFactory->redirect('/admin/blog/edit/' . $post->slug);
    }

    /** @return string[] */
    public function validate(Request $request): array
    {
        $errors = [];

        $title = trim($request->get('title') ?? '');
        $slugInput = trim($request->get('slug') ?? '');
        $excerpt = trim($request->get('excerpt') ?? '');
        $content = trim($request->get('content') ?? '');
        $status = trim($request->get('status') ?? 'draft');

        if ($title === '') {
            $errors[] = 'Title is required.';
        }

        if ($slugInput === '') {
            $errors[] = 'Slug is required.';
        }

        if (!in_array($status, ['draft', 'published'], true)) {
            $errors[] = 'Invalid status selected.';
        }

        return $errors;
    }
}
