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

    public function index(): Response
    {
        return $this->responseFactory->view('blog/index.html.twig', [
            'posts' => $this->blogRepository->findPublished()
        ]);
    }

    public function show(Request $request): Response
    {
        $slug = $request->get('slug');

        if ($slug === null) {
            return new Response('Blog post not found', 404);
        }

        $post = $this->blogRepository->findPublishedBySlug($slug);

        if ($post === null) {
            return new Response('Blog post not found', 404);
        }

        return $this->responseFactory->view('blog/show.html.twig', [
            'post' => $post
        ]);
    }
}
