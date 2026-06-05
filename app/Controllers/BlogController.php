<?php

namespace App\Controllers;

use App\Models\BlogPost;
use App\Repositories\BlogRepositoryInterface;
use App\Services\ImageUploadService;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class BlogController
{
    private ResponseFactory $responseFactory;

    private BlogRepositoryInterface $blogRepository;

    private ImageUploadService $uploadImageService;

    public function __construct(
        ResponseFactory $responseFactory,
        BlogRepositoryInterface $blogRepository,
        ImageUploadService $uploadImageService
    ) {
        $this->responseFactory = $responseFactory;
        $this->blogRepository = $blogRepository;
        $this->uploadImageService = $uploadImageService;
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

        $title = trim((string) ($request->get('title') ?? ''));
        $slug = trim((string) ($request->get('slug') ?? ''));
        $excerpt = trim((string) ($request->get('excerpt') ?? ''));
        $content = trim((string) ($request->get('content') ?? ''));
        $status = trim((string) ($request->get('status') ?? 'draft'));

        $values = [
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'content' => $content,
            'status' => $status,
        ];

        if (!empty($errors)) {
            return $this->responseFactory->view('blog/create.html.twig', [
                'errors' => $errors,
                'values' => $values,
            ]);
        }

        try {
            $cardImage = $this->uploadImageService->uploadImage(
                'card_image_upload',
                'blog'
            );

            $heroImage = $this->uploadImageService->uploadImage(
                'hero_image_upload',
                'blog'
            );
        } catch (\RuntimeException $exception) {
            return $this->responseFactory->view('blog/create.html.twig', [
                'errors' => [$exception->getMessage()],
                'values' => $values,
            ]);
        }

        if ($cardImage === null || $cardImage === '') {
            $errors[] = 'Card image is required.';
        }

        if ($heroImage === null || $heroImage === '') {
            $errors[] = 'Hero image is required.';
        }

        if (!empty($errors)) {
            return $this->responseFactory->view('blog/create.html.twig', [
                'errors' => $errors,
                'values' => $values,
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

        $post->title = trim((string) ($request->get('title') ?? ''));
        $post->slug = trim((string) ($request->get('slug') ?? ''));
        $post->excerpt = trim((string) ($request->get('excerpt') ?? ''));
        $post->content = trim((string) ($request->get('content') ?? ''));
        $post->status = trim((string) ($request->get('status') ?? 'draft'));

        try {
            $post->cardImage = $this->uploadImageService->uploadImage(
                'card_image_upload',
                'blog',
                $post->cardImage
            ) ?? $post->cardImage;

            $post->heroImage = $this->uploadImageService->uploadImage(
                'hero_image_upload',
                'blog',
                $post->heroImage
            ) ?? $post->heroImage;
        } catch (\RuntimeException $exception) {
            return $this->responseFactory->view('blog/edit.html.twig', [
                'post' => $post,
                'errors' => [$exception->getMessage()],
            ]);
        }

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

        $title = trim((string) ($request->get('title') ?? ''));
        $slug = trim((string) ($request->get('slug') ?? ''));
        $excerpt = trim((string) ($request->get('excerpt') ?? ''));
        $content = trim((string) ($request->get('content') ?? ''));
        $status = trim((string) ($request->get('status') ?? 'draft'));

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
}