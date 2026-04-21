<?php

namespace App\Security;

use App\Entity\User;
use GuzzleHttp\Client;
use JMS\Serializer\Serializer;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GithubUserProvider implements UserProviderInterface
{
    private Client $client;
    private Serializer $serializer;

    public function __construct(Client $client, Serializer $serializer)
    {
        $this->client = $client;
        $this->serializer = $serializer;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $response = $this->client->get('https://api.github.com/user?access_token=' . $identifier);
        $result = $response->getBody()->getContents();
        $userData = $this->serializer->deserialize($result, 'array', 'json');

        if (!$userData) {
            throw new \LogicException('Impossible de récupérer les informations utilisateur depuis GitHub.');
        }

        return new User(
            $userData['login'] ?? '',
            $userData['name'] ?? '',
            $userData['email'] ?? '',
            $userData['avatar_url'] ?? '',
            $userData['html_url'] ?? ''
        );
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return $class === User::class;
    }
}
