<?php

namespace Middleware;

use App\Middleware\CsrfMiddleware;
use App\Services\CsrfService;
use Framework\Request;
use Framework\Response;
use Framework\Session;
use PHPUnit\Framework\TestCase;

class CsrfMiddlewareTest extends TestCase
{
    public function testGetRequestPassesWithoutTokenValidation(): void
    {
        $session = $this->createMock(Session::class);

        $request = $this
            ->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request->session = $session;
        $request->method = 'GET';

        $csrfService = $this
            ->getMockBuilder(CsrfService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setSession', 'validateToken'])
            ->getMock();

        $csrfService
            ->expects($this->once())
            ->method('setSession')
            ->with($session);

        $csrfService
            ->expects($this->never())
            ->method('validateToken');

        $middleware = new CsrfMiddleware($csrfService);

        $nextResponse = new Response('Next response', 200);

        $response = $middleware->handle($request, function () use ($nextResponse): Response {
            return $nextResponse;
        });

        $this->assertSame($nextResponse, $response);
    }

    public function testPostRequestWithValidTokenPasses(): void
    {
        $session = $this->createMock(Session::class);

        $request = $this
            ->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();

        $request->session = $session;
        $request->method = 'POST';

        $request
            ->method('get')
            ->with('csrf_token')
            ->willReturn('valid-token');

        $csrfService = $this
            ->getMockBuilder(CsrfService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setSession', 'validateToken'])
            ->getMock();

        $csrfService
            ->expects($this->once())
            ->method('setSession')
            ->with($session);

        $csrfService
            ->expects($this->once())
            ->method('validateToken')
            ->with('valid-token')
            ->willReturn(true);

        $middleware = new CsrfMiddleware($csrfService);

        $nextResponse = new Response('Post accepted', 200);

        $response = $middleware->handle($request, function () use ($nextResponse): Response {
            return $nextResponse;
        });

        $this->assertSame($nextResponse, $response);
    }

    public function testPostRequestWithInvalidTokenReturnsForbidden(): void
    {
        $session = $this->createMock(Session::class);

        $request = $this
            ->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();

        $request->session = $session;
        $request->method = 'POST';

        $request
            ->method('get')
            ->with('csrf_token')
            ->willReturn('invalid-token');

        $csrfService = $this
            ->getMockBuilder(CsrfService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setSession', 'validateToken'])
            ->getMock();

        $csrfService
            ->expects($this->once())
            ->method('setSession')
            ->with($session);

        $csrfService
            ->expects($this->once())
            ->method('validateToken')
            ->with('invalid-token')
            ->willReturn(false);

        $middleware = new CsrfMiddleware($csrfService);

        $response = $middleware->handle($request, function (): Response {
            return new Response('Should not be called', 200);
        });

        $this->assertSame(403, $response->responseCode);
        $this->assertSame('Forbidden: Invalid CSRF token', $response->body);
    }
}
