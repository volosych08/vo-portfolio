<?php

namespace Framework;

class ResponseFactory
{
    private \Twig\Environment $twig;

    /** @var array<string|int, mixed> */
    public array $globalContext = [];

    public function __construct(bool $debugMode, string $viewsPath)
    {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../' . $viewsPath);
        $twig = new \Twig\Environment($loader, [
            'debug' => $debugMode
        ]);
        if ($debugMode) {
            $twig->addExtension(new \Twig\Extension\DebugExtension());
        }
        $this->twig = $twig;
    }

    public function addFunction(string $name, callable $function): void
    {
        $this->twig->addFunction(new \Twig\TwigFunction($name, $function));
    }

    public function addStringFunction(string $name, callable $function): void
    {
        $function = function () use ($function) {
            return new \Twig\Markup($function(), 'UTF-8');
        };
        $this->twig->addFunction(new \Twig\TwigFunction($name, $function));
    }

    /**
     * @param string $view
     * @param array<mixed> $context
     * @return Response
     */
    public function view(string $view, array $context = []): Response
    {
        $response = new Response();

        try {
            $response->responseCode = 200;
            $response->body = $this->twig->render($view, array_merge($this->globalContext, $context));
            return $response;
        } catch (\Exception $e) {
            $response->responseCode = 500;
            $response->body = $e->getMessage();
            return $response;
        }
    }

    public function notFound(): Response
    {
        $response = new Response();
        try {
            $response->responseCode = 404;
            $response->body = $this->twig->render('404.html.twig');
            return $response;
        } catch (\Exception $e) {
            $response->responseCode = 500;
            $response->body = $e->getMessage();
            return $response;
        }
    }

    public function internalError(): Response
    {
        $response = new Response();
        try {
            $response->responseCode = 500;
            $response->body = $this->twig->render('500.html.twig');
            return $response;
        } catch (\Exception $e) {
            $response->responseCode = 500;
            $response->body = $e->getMessage();
            return $response;
        }
    }

    public function notAuthorized(): Response
    {
        $response = new Response();
        $response->responseCode = 401;
        $response->body = "Unauthorized";
        return $response;
    }

    public function redirect(string $url): Response
    {
        $response = new Response();
        $response->responseCode = 302;
        $response->header = "Location: " . $url;
        return $response;
    }
}
