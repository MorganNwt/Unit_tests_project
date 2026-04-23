<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User(
            'octocat',
            'The Octocat',
            'octocat@github.com',
            'https://github.com/avatar.png',
            'https://github.com/octocat'
        );
    }

    // Test de l'ID est un peu particulier car il est généralement géré par la base de données et n'a pas de setter public.
    public function testGetId(): void
    {
        $reflection = new \ReflectionClass($this->user);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->user, 1);

        $this->assertSame(1, $this->user->getId());
    }

    // Les autres tests vérifient les getters et les méthodes de l'entité User.
    public function testGetUsername(): void
    {
        $this->assertSame('octocat', $this->user->getUsername());
    }

    public function testGetFullname(): void
    {
        $this->assertSame('The Octocat', $this->user->getFullname());
    }

    public function testGetEmail(): void
    {
        $this->assertSame('octocat@github.com', $this->user->getEmail());
    }

    public function testGetAvatarUrl(): void
    {
        $this->assertSame('https://github.com/avatar.png', $this->user->getAvatarUrl());
    }

    public function testGetProfileHtmlUrl(): void
    {
        $this->assertSame('https://github.com/octocat', $this->user->getProfileHtmlUrl());
    }

    public function testGetRoles(): void
    {
        $this->assertSame(['ROLE_USER'], $this->user->getRoles());
    }

    public function testEraseCredentials(): void
    {
        $this->user->eraseCredentials();
        $this->assertTrue(true);
    }

    // Test de la méthode getUserIdentifier qui doit retourner le username.
    public function testGetUserIdentifier(): void
    {
        $this->assertSame('octocat', $this->user->getUserIdentifier());
    }
}
