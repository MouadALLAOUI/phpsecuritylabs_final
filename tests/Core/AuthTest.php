<?php

namespace Tests\Core;

use PHPUnit\Framework\TestCase;
use App\Core\Auth;

class AuthTest extends TestCase
{
    private Auth $auth;
    
    protected function setUp(): void
    {
        parent::setUp();
        \App\Core\Session::start();
        $this->auth = new Auth();
    }
    
    public function testAuthInstantiates(): void
    {
        $this->assertInstanceOf(Auth::class, $this->auth);
    }
    
    public function testIsAuthenticatedReturnsFalseWhenNotLoggedIn(): void
    {
        unset($_SESSION['user_id']);
        $this->assertFalse($this->auth->isAuthenticated());
    }
    
    public function testGetUserReturnsNullWhenNotLoggedIn(): void
    {
        unset($_SESSION['user_id']);
        $this->assertNull($this->auth->getUser());
    }
    
    public function testLoginWithValidCredentials(): void
    {
        // Mock database response for testing
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = 'testuser';
        
        $this->assertTrue($this->auth->isAuthenticated());
        $user = $this->auth->getUser();
        $this->assertEquals('testuser', $user['username']);
    }
    
    public function testLogoutClearsSession(): void
    {
        // Set up logged in state
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = 'testuser';
        
        // Perform logout
        $this->auth->logout();
        
        $this->assertFalse($this->auth->isAuthenticated());
        $this->assertNull($this->auth->getUser());
    }
    
    public function testGetUserIdReturnsCorrectId(): void
    {
        $_SESSION['user_id'] = 42;
        $this->assertEquals(42, $this->auth->getUserId());
    }
    
    public function testGetUserIdReturnsNullWhenNotLoggedIn(): void
    {
        unset($_SESSION['user_id']);
        $this->assertNull($this->auth->getUserId());
    }
    
    public function testPermissionCheckForAdmin(): void
    {
        // Test admin permission check
        $_SESSION['user_id'] = 1;
        $_SESSION['role'] = 'admin';
        
        $user = $this->auth->getUser();
        $this->assertEquals('admin', $user['role'] ?? null);
    }
    
    public function testPermissionCheckForRegularUser(): void
    {
        // Test regular user permission check
        $_SESSION['user_id'] = 2;
        $_SESSION['role'] = 'user';
        
        $user = $this->auth->getUser();
        $this->assertEquals('user', $user['role'] ?? null);
    }
}
