<?php

namespace App\Controllers;

use App\Models\BlogPost;
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

    public function create(): Response
    {
        return $this->responseFactory->view('blog/create.html.twig', [
            'errors' => [],
            'values' => [
                'title' => '',
                'slug' => '',
                'excerpt' => '',
                'content' => '',
                'status' => 'draft',
            ],
        ]);
    }

    public function store(Request $request): Response
    {
        $errors = $this->validate($request);

        $title = trim($request->get('title') ?? '');
        $slug = trim($request->get('slug') ?? '');
        $excerpt = trim($request->get('excerpt') ?? '');
        $content = trim($request->get('content') ?? '');
        $status = trim($request->get('status') ?? 'draft');

        $cardImage = '';
        $heroImage = '';

        if (empty($errors)) {
            $cardImage = $this->uploadBlogImage('card_image_upload', '');
            $heroImage = $this->uploadBlogImage('hero_image_upload', '');

            if ($cardImage === '') {
                $errors[] = 'Card image is required.';
            }

            if ($heroImage === '') {
                $errors[] = 'Hero image is required.';
            }
        }

        if (!empty($errors)) {
            return $this->responseFactory->view('blog/create.html.twig', [
                'errors' => $errors,
                'values' => [
                    'title' => $title,
                    'slug' => $slug,
                    'excerpt' => $excerpt,
                    'content' => $content,
                    'status' => $status,
                ],
            ]);
        }

        $now = date('Y-m-d H:i:s');

        $post = new BlogPost();
        $post->title = $title;
        $post->slug = $slug;
        $post->excerpt = $excerpt;
        $post->content = $content;
        $post->status = $status;
        $post->cardImage = $cardImage;
        $post->heroImage = $heroImage;
        $post->publishedAt = date('Y-m-d');
        $post->createdAt = $now;
        $post->updatedAt = $now;

        $this->blogRepository->insert($post);

        return $this->responseFactory->redirect('/admin/blog/edit/' . $post->slug);
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

    public function update(Request $request): Response
    {
        $slug = $request->routeParameters['slug'] ?? null;

        if ($slug === null) {
            return new Response('Blog post not found', 404);
        }

        $post = $this->blogRepository->findBySlug($slug);

        if ($post === null) {
            return new Response('Blog post not found', 404);
        }

        $errors = $this->validate($request, $post);

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

    public function delete(Request $request): Response
    {
        $slug = $request->routeParameters['slug'] ?? null;

        if ($slug === null) {
            return new Response('Blog post not found', 404);
        }

        $post = $this->blogRepository->findBySlug($slug);

        if ($post === null) {
            return new Response('Blog post not found', 404);
        }

        $this->blogRepository->delete($post);

        return $this->responseFactory->redirect('/blog');
    }

    /**
     * @return string[]
     */
    private function validate(Request $request, ?BlogPost $currentPost = null): array
    {
        $errors = [];

        $title = trim($request->get('title') ?? '');
        $slug = trim($request->get('slug') ?? '');
        $excerpt = trim($request->get('excerpt') ?? '');
        $content = trim($request->get('content') ?? '');
        $status = trim($request->get('status') ?? 'draft');

        if ($title === '') {
            $errors[] = 'Title is required.';
        }

        if ($slug === '') {
            $errors[] = 'Slug is required.';
        }

        if ($slug !== '' && !preg_match('/^[a-z0-9-]+$/', $slug)) {
            $errors[] = 'Slug may only contain lowercase letters, numbers, and dashes.';
        }

        if ($excerpt === '') {
            $errors[] = 'Excerpt is required.';
        }

        if ($content === '') {
            $errors[] = 'Content is required.';
        }

        if (!in_array($status, ['draft', 'published'], true)) {
            $errors[] = 'Invalid status selected.';
        }

        if ($slug !== '') {
            $existingPost = $this->blogRepository->findBySlug($slug);

            if ($existingPost !== null && ($currentPost === null || $existingPost->id !== $currentPost->id)) {
                $errors[] = 'A blog post with this slug already exists.';
            }
        }

        return $errors;
    }

    private function uploadBlogImage(string $fieldName, string $currentPath): string
    {
        if (!isset($_FILES[$fieldName])) {
            return $currentPath;
        }

        $file = $_FILES[$fieldName];

        if (!is_array($file)) {
            return $currentPath;
        }

        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return $currentPath;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return $currentPath;
        }

        $maxSize = 10 * 1024 * 1024;

        if ($file['size'] > $maxSize) {
            return $currentPath;
        }

        $allowedMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        $mimeType = mime_content_type($file['tmp_name']);

        if ($mimeType === false || !isset($allowedMimeTypes[$mimeType])) {
            return $currentPath;
        }

        $extension = $allowedMimeTypes[$mimeType];
        $fileName = uniqid('blog_', true) . '.' . $extension;

        $uploadDir = __DIR__ . '/../../public/uploads/blog';

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            return $currentPath;
        }

        $targetPath = $uploadDir . '/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $currentPath;
        }

        return '/uploads/blog/' . $fileName;
    }
}