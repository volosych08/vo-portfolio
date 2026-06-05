<?php

namespace Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use Framework\Session;
use PHPUnit\Framework\TestCase;

class AuthServiceTest extends TestCase
{
    public function testRegisterHashesPasswordAndInsertsUser(): void
    {
        $user = new User();
        $user->username = 'newuser';
        $user->name = 'New User';
        $user->role = 'user';

        $userRepository = $this
            ->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['insert'])
            ->getMock();

        $userRepository
            ->expects($this->once())
            ->method('insert')
            ->with($this->callback(function (User $user): bool {
                return password_verify('secret-password', $user->password);
            }))
            ->willReturnCallback(function (User $user): User {
                $user->id = 1;
                return $user;
            });

        $authService = new AuthService($userRepository);

        $registeredUser = $authService->register($user, 'secret-password');

        $this->assertSame(1, $registeredUser->id);
        $this->assertNotSame('secret-password', $registeredUser->password);
        $this->assertTrue(password_verify('secret-password', $registeredUser->password));
    }

    public function testLoginWithCorrectCredentialsReturnsUserAndSetsSession(): void
    {
        $user = new User();
        $user->id = 5;
        $user->username = 'admin';
        $user->password = password_hash('correct-password', PASSWORD_DEFAULT);
        $user->name = 'Admin User';
        $user->role = 'admin';

        $userRepository = $this
            ->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findByUsername'])
            ->getMock();

        $userRepository
            ->expects($this->once())
            ->method('findByUsername')
            ->with('admin')
            ->willReturn($user);

        $session = $this
            ->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['destroy', 'set'])
            ->getMock();

        $session
            ->expects($this->once())
            ->method('destroy');

        $session
            ->expects($this->once())
            ->method('set')
            ->with('user_id', 5);

        $authService = new AuthService($userRepository);

        $result = $authService->loginWithCredentials('admin', 'correct-password', $session);

        $this->assertSame($user, $result);
    }

    public function testLoginWithWrongPasswordReturnsFalse(): void
    {
        $user = new User();
        $user->id = 5;
        $user->username = 'admin';
        $user->password = password_hash('correct-password', PASSWORD_DEFAULT);
        $user->name = 'Admin User';
        $user->role = 'admin';

        $userRepository = $this
            ->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findByUsername'])
            ->getMock();

        $userRepository
            ->method('findByUsername')
            ->with('admin')
            ->willReturn($user);

        $session = $this
            ->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['destroy', 'set'])
            ->getMock();

        $session
            ->expects($this->never())
            ->method('destroy');

        $session
            ->expects($this->never())
            ->method('set');

        $authService = new AuthService($userRepository);

        $result = $authService->loginWithCredentials('admin', 'wrong-password', $session);

        $this->assertFalse($result);
    }

    public function testLoginWithMissingUserReturnsFalse(): void
    {
        $userRepository = $this
            ->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findByUsername'])
            ->getMock();

        $userRepository
            ->method('findByUsername')
            ->with('missing')
            ->willReturn(null);

        $session = $this
            ->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['destroy', 'set'])
            ->getMock();

        $session
            ->expects($this->never())
            ->method('destroy');

        $session
            ->expects($this->never())
            ->method('set');

        $authService = new AuthService($userRepository);

        $result = $authService->loginWithCredentials('missing', 'password', $session);

        $this->assertFalse($result);
    }

    public function testForceLoginSetsSessionUserId(): void
    {
        $user = new User();
        $user->id = 10;

        $userRepository = $this
            ->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $session = $this
            ->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['destroy', 'set'])
            ->getMock();

        $session
            ->expects($this->once())
            ->method('destroy');

        $session
            ->expects($this->once())
            ->method('set')
            ->with('user_id', 10);

        $authService = new AuthService($userRepository);

        $authService->forceLogin($user, $session);
    }

    public function testValidateUserReturnsUserFromRepository(): void
    {
        $user = new User();
        $user->id = 7;

        $userRepository = $this
            ->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findById'])
            ->getMock();

        $userRepository
            ->expects($this->once())
            ->method('findById')
            ->with(7)
            ->willReturn($user);

        $authService = new AuthService($userRepository);

        $this->assertSame($user, $authService->validateUser(7));
    }

    public function testLogoutDestroysSession(): void
    {
        $userRepository = $this
            ->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $session = $this
            ->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['destroy'])
            ->getMock();

        $session
            ->expects($this->once())
            ->method('destroy');

        $authService = new AuthService($userRepository);

        $authService->logout($session);
    }
}
