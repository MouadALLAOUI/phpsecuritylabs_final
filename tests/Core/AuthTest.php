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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
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
}
