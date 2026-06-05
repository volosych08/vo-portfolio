<?php

namespace Services;

use App\Services\CsrfService;
use Framework\ResponseFactory;
use Framework\Session;
use PHPUnit\Framework\TestCase;

class CsrfServiceTest extends TestCase
{
    public function testGetTokenReturnsExistingToken(): void
    {
        $responseFactory = $this->createResponseFactoryMock();

        $session = $this
            ->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get', 'set'])
            ->getMock();

        $session
            ->expects($this->once())
            ->method('get')
            ->with('csrf_token')
            ->willReturn('existing-token');

        $session
            ->expects($this->never())
            ->method('set');

        $csrfService = new CsrfService($responseFactory);

        $this->assertSame('existing-token', $csrfService->getToken($session));
    }

    public function testGetTokenCreatesNewTokenWhenMissing(): void
    {
        $responseFactory = $this->createResponseFactoryMock();

        $session = $this
            ->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get', 'set'])
            ->getMock();

        $session
            ->expects($this->once())
            ->method('get')
            ->with('csrf_token')
            ->willReturn(null);

        $session
            ->expects($this->once())
            ->method('set')
            ->with(
                'csrf_token',
                $this->callback(
                    fn (string $token): bool => strlen($token) === 64
                )
            );

        $csrfService = new CsrfService($responseFactory);

        $token = $csrfService->getToken($session);

        $this->assertSame(64, strlen($token));
    }

    /**
     * @throws \Exception
     */
    public function testValidateTokenReturnsTrueForValidToken(): void
    {
        $responseFactory = $this->createResponseFactoryMock();

        $session = $this
            ->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();

        $session
            ->method('get')
            ->with('csrf_token')
            ->willReturn('valid-token');

        $csrfService = new CsrfService($responseFactory);
        $csrfService->setSession($session);

        $this->assertTrue($csrfService->validateToken('valid-token'));
    }

    /**
     * @throws \Exception
     */
    public function testValidateTokenReturnsFalseForInvalidToken(): void
    {
        $responseFactory = $this->createResponseFactoryMock();

        $session = $this
            ->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();

        $session
            ->method('get')
            ->with('csrf_token')
            ->willReturn('valid-token');

        $csrfService = new CsrfService($responseFactory);
        $csrfService->setSession($session);

        $this->assertFalse($csrfService->validateToken('wrong-token'));
    }

    /**
     * @throws \Exception
     */
    public function testValidateTokenReturnsFalseWhenSessionTokenIsMissing(): void
    {
        $responseFactory = $this->createResponseFactoryMock();

        $session = $this
            ->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();

        $session
            ->method('get')
            ->with('csrf_token')
            ->willReturn(null);

        $csrfService = new CsrfService($responseFactory);
        $csrfService->setSession($session);

        $this->assertFalse($csrfService->validateToken('anything'));
    }

    public function testValidateTokenThrowsExceptionWhenSessionIsNotSet(): void
    {
        $responseFactory = $this->createResponseFactoryMock();

        $csrfService = new CsrfService($responseFactory);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Session not set in CsrfService');

        $csrfService->validateToken('token');
    }

    private function createResponseFactoryMock(): ResponseFactory
    {
        $responseFactory = $this
            ->getMockBuilder(ResponseFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['addFunction', 'addStringFunction'])
            ->getMock();

        $responseFactory
            ->expects($this->once())
            ->method('addFunction');

        $responseFactory
            ->expects($this->once())
            ->method('addStringFunction');

        return $responseFactory;
    }
}
