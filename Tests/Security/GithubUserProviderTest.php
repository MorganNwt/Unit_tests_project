<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Security\GithubUserProvider;
use GuzzleHttp\ClientInterface;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;

class GithubUserProviderTest extends TestCase
{
    // Test de la méthode loadUserByIdentifier pour vérifier que les données utilisateur sont correctement récupérées et utilisées pour créer une instance de User
    public function testLoadUserByIdentifierReturnsUser(): void
    {
        $identifier = 'fake_access_token';

        $json = json_encode([
            'login' => 'octocat',
            'name' => 'The Octocat',
            'email' => 'octocat@github.com',
            'avatar_url' => 'https://github.com/images/error/octocat_happy.gif',
            'html_url' => 'https://github.com/octocat',
        ]);

        $stream = $this->createMock(StreamInterface::class);
        $stream
            ->expects($this->once())
            ->method('getContents')
            ->willReturn($json);

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $client = $this->createMock(ClientInterface::class);
        $client
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'https://api.github.com/user?access_token=' . $identifier)
            ->willReturn($response);

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with($json, 'array', 'json')
            ->willReturn([
                'login' => 'octocat',
                'name' => 'The Octocat',
                'email' => 'octocat@github.com',
                'avatar_url' => 'https://github.com/images/error/octocat_happy.gif',
                'html_url' => 'https://github.com/octocat',
            ]);

        $provider = new GithubUserProvider($client, $serializer);

        $user = $provider->loadUserByIdentifier($identifier);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('octocat', $user->getUsername());
        $this->assertSame('The Octocat', $user->getFullname());
        $this->assertSame('octocat@github.com', $user->getEmail());
        $this->assertSame('https://github.com/images/error/octocat_happy.gif', $user->getAvatarUrl());
        $this->assertSame('https://github.com/octocat', $user->getProfileHtmlUrl());
    }

    // Test de la méthode loadUserByIdentifier pour vérifier que l'exception est levée lorsque les données utilisateur sont vides
    public function testLoadUserByIdentifierThrowsLogicExceptionWhenUserDataIsEmpty(): void
    {
        $identifier = 'fake_access_token';
        $json = '{}';

        $stream = $this->createMock(StreamInterface::class);
        $stream
            ->expects($this->once())
            ->method('getContents')
            ->willReturn($json);

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $client = $this->createMock(ClientInterface::class);
        $client
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'https://api.github.com/user?access_token=' . $identifier)
            ->willReturn($response);

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with($json, 'array', 'json')
            ->willReturn([]);

        $provider = new GithubUserProvider($client, $serializer);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Impossible de récupérer les informations utilisateur depuis GitHub.');

        $provider->loadUserByIdentifier($identifier);
    }

    // Test de la méthode loadUserByIdentifier pour vérifier que les champs manquants sont remplacés par des chaînes vides
    public function testLoadUserByIdentifierUsesEmptyStringsWhenFieldsAreMissing(): void
    {
        $identifier = 'fake_access_token';
        $json = json_encode([
            'login' => 'octocat',
        ]);

        $stream = $this->createMock(StreamInterface::class);
        $stream
            ->expects($this->once())
            ->method('getContents')
            ->willReturn($json);

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $client = $this->createMock(ClientInterface::class);
        $client
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'https://api.github.com/user?access_token=' . $identifier)
            ->willReturn($response);

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with($json, 'array', 'json')
            ->willReturn([
                'login' => 'octocat',
            ]);

        $provider = new GithubUserProvider($client, $serializer);

        $user = $provider->loadUserByIdentifier($identifier);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('octocat', $user->getUsername());
        $this->assertSame('', $user->getFullname());
        $this->assertSame('', $user->getEmail());
        $this->assertSame('', $user->getAvatarUrl());
        $this->assertSame('', $user->getProfileHtmlUrl());
    }

    // Test de la méthode refreshUser pour une instance de User valide
    public function testRefreshUserReturnsSameUser(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $serializer = $this->createMock(SerializerInterface::class);

        $provider = new GithubUserProvider($client, $serializer);

        $user = new User(
            'octocat',
            'The Octocat',
            'octocat@github.com',
            'avatar',
            'profile'
        );

        $result = $provider->refreshUser($user);

        $this->assertSame($user, $result);
    }

    // Test de la méthode refreshUser pour une instance de User non supportée
    public function testRefreshUserThrowsExceptionForUnsupportedUser(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $serializer = $this->createMock(SerializerInterface::class);

        $provider = new GithubUserProvider($client, $serializer);

        $fakeUser = $this->createMock(UserInterface::class);

        $this->expectException(UnsupportedUserException::class);

        $provider->refreshUser($fakeUser);
    }

    // Test de la méthode supportsClass pour la classe User
    public function testSupportsClassReturnsTrueForUserClass(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $serializer = $this->createMock(SerializerInterface::class);

        $provider = new GithubUserProvider($client, $serializer);

        $this->assertTrue($provider->supportsClass(User::class));
    }

    // Test de la méthode supportsClass pour une classe non supportée
    public function testSupportsClassReturnsFalseForOtherClass(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $serializer = $this->createMock(SerializerInterface::class);

        $provider = new GithubUserProvider($client, $serializer);

        $this->assertFalse($provider->supportsClass(\stdClass::class));
    }
}
