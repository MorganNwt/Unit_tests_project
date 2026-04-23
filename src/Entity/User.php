<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table
 */
class User implements UserInterface
{
    public const MAX_ADVICED_DAILY_CALORIES = 2500;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column
     */
    private string $username;

    /**
     * @ORM\Column
     */
    private string $fullname;

    /**
     * @ORM\Column
     */
    private string $email;

    /**
     * @ORM\Column
     */
    private string $avatarUrl;

    /**
     * @ORM\Column
     */
    private string $profileHtmlUrl;

    public function __construct(
        string $username,
        string $fullname,
        string $email,
        string $avatarUrl,
        string $profileHtmlUrl
    ) {
        $this->username = $username;
        $this->fullname = $fullname;
        $this->email = $email;
        $this->avatarUrl = $avatarUrl;
        $this->profileHtmlUrl = $profileHtmlUrl;
    }

    public function getId(): mixed
    {
        return $this->id;
    }

    public function getUsername(): mixed
    {
        return $this->username;
    }

    public function getFullname(): mixed
    {
        return $this->fullname;
    }

    public function getEmail(): mixed
    {
        return $this->email;
    }

    public function getAvatarUrl(): mixed
    {
        return $this->avatarUrl;
    }

    public function getProfileHtmlUrl(): mixed
    {
        return $this->profileHtmlUrl;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void {}

    public function getUserIdentifier(): string
    {
        return $this->username;
    }
}
