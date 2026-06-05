<?php

namespace Middleware;

use App\Middleware\AuthMiddleware;
use App\Models\User;
use App\Services\AuthService;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;
use Framework\Session;
use PHPUnit\Framework\TestCase;

class AuthMiddlewareTest extends TestCase
{
    public function testHandleAddsValidUserToRequest(): void
    {
        $user = new User();
        $user->id = 1;
        $user->username = 'admin';
        $user->role = 'admin';

        $session = $this->createMock(Session::class);
        $session
            ->method('get')
            ->with('user_id')
            ->willReturn('1');

        $request = $this
            ->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $request->session = $session;

        $request
            ->expects($this->once())
            ->method('setAttribute')
            ->with('user', $user);

        $authService = $this
            ->getMockBuilder(AuthService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['validateUser'])
            ->getMock();

        $authService
            ->expects($this->once())
            ->method('validateUser')
            ->with(1)
            ->willReturn($user);

        $responseFactory = $this
            ->getMockBuilder(ResponseFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $middleware = new AuthMiddleware($authService, $responseFactory);

        $nextResponse = new Response('Next response', 200);

        $response = $middleware->handle($request, function (Request $request) use ($nextResponse): Response {
            return $nextResponse;
        });

        $this->assertSame($nextResponse, $response);
    }

    public function testRequireAuthReturnsNotAuthorizedWhenUserIsMissing(): void
    {
        $request = $this
            ->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $request
            ->method('getAttribute')
            ->with('user')
            ->willReturn(null);

        $authService = $this
            ->getMockBuilder(AuthService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $notAuthorizedResponse = new Response('Not authorized', 401);

        $responseFactory = $this
            ->getMockBuilder(ResponseFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['notAuthorized'])
            ->getMock();

        $responseFactory
            ->expects($this->once())
            ->method('notAuthorized')
            ->willReturn($notAuthorizedResponse);

        $middleware = new AuthMiddleware($authService, $responseFactory);

        $response = $middleware->requireAuth($request, function (): Response {
            return new Response('Should not be called', 200);
        });

        $this->assertSame($notAuthorizedResponse, $response);
    }

    public function testRequireAuthCallsNextWhenUserExists(): void
    {
        $user = new User();
        $user->id = 1;
        $user->username = 'admin';
        $user->role = 'admin';

        $request = $this
            ->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $request
            ->method('getAttribute')
            ->with('user')
            ->willReturn($user);

        $authService = $this
            ->getMockBuilder(AuthService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseFactory = $this
            ->getMockBuilder(ResponseFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $middleware = new AuthMiddleware($authService, $responseFactory);

        $nextResponse = new Response('Allowed', 200);

        $response = $middleware->requireAuth($request, function () use ($nextResponse): Response {
            return $nextResponse;
        });

        $this->assertSame($nextResponse, $response);
    }

    public function testRequireAdminReturnsNotAuthorizedWhenUserIsNotAdmin(): void
    {
        $user = new User();
        $user->id = 2;
        $user->username = 'normal-user';
        $user->role = 'user';

        $request = $this
            ->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $request
            ->method('getAttribute')
            ->with('user')
            ->willReturn($user);

        $authService = $this
            ->getMockBuilder(AuthService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $notAuthorizedResponse = new Response('Not authorized', 401);

        $responseFactory = $this
            ->getMockBuilder(ResponseFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['notAuthorized'])
            ->getMock();

        $responseFactory
            ->expects($this->once())
            ->method('notAuthorized')
            ->willReturn($notAuthorizedResponse);

        $middleware = new AuthMiddleware($authService, $responseFactory);

        $response = $middleware->requireAdmin($request, function (): Response {
            return new Response('Should not be called', 200);
        });

        $this->assertSame($notAuthorizedResponse, $response);
    }

    public function testRequireAdminCallsNextWhenUserIsAdmin(): void
    {
        $user = new User();
        $user->id = 1;
        $user->username = 'admin';
        $user->role = 'admin';

        $request = $this
            ->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $request
            ->method('getAttribute')
            ->with('user')
            ->willReturn($user);

        $authService = $this
            ->getMockBuilder(AuthService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseFactory = $this
            ->getMockBuilder(ResponseFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $middleware = new AuthMiddleware($authService, $responseFactory);

        $nextResponse = new Response('Admin allowed', 200);

        $response = $middleware->requireAdmin($request, function () use ($nextResponse): Response {
            return $nextResponse;
        });

        $this->assertSame($nextResponse, $response);
    }
}
