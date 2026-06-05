<?php

namespace App\Controllers;

use Framework\Response;
use Framework\ResponseFactory;

abstract class ApiBaseController
{
    public function __construct(protected ResponseFactory $responseFactory)
    {
    }

    protected function apiJson(
        string $resource,
        mixed $data,
        string $selfLink,
        ?string $collectionLink = null,
        ?int $total = null
    ): Response {
        $meta = ['resource' => $resource];

        if ($total !== null) {
            $meta['total'] = $total;
        }

        $links = ['self' => $selfLink];

        if ($collectionLink !== null) {
            $links['collection'] = $collectionLink;
        }

        return $this->responseFactory->json([
            'meta' => $meta,
            'links' => $links,
            'data' => $data,
        ]);
    }

    protected function apiError(
        string $resource,
        string $message,
        string $selfLink,
        int $status,
        ?string $collectionLink = null
    ): Response {
        $links = ['self' => $selfLink];

        if ($collectionLink !== null) {
            $links['collection'] = $collectionLink;
        }

        return $this->responseFactory->json([
            'meta' => [
                'resource' => $resource,
            ],
            'links' => $links,
            'error' => [
                'status' => $status,
                'message' => $message,
            ],
        ], $status);
    }
}
