<?php

namespace App\Controllers;

use Framework\Response;
use Framework\ResponseFactory;

class ProfileController
{
    private ResponseFactory $responseFactory;

    public function __construct(ResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function index(): Response
    {
        return $this->responseFactory->view("profile/index.html.twig");
    }
}