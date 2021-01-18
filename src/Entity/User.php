<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private string $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     */
    private string $googleId;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private string $refreshToken;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private string $accessToken;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private DateTime $accessTokenExpiresAt;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private bool $revoked = false;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $avatar;

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private array $propertySearchSettings = [];

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getGoogleId(): string
    {
        return $this->googleId;
    }

    public function setGoogleId(string $googleId): User
    {
        $this->googleId = $googleId;

        return $this;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(string $refreshToken): User
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): User
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getAccessTokenExpiresAt(): DateTime
    {
        return $this->accessTokenExpiresAt;
    }

    public function setAccessTokenExpiresAt(DateTime $accessTokenExpiresAt): User
    {
        $this->accessTokenExpiresAt = $accessTokenExpiresAt;

        return $this;
    }

    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    public function setRevoked(bool $revoked): User
    {
        $this->revoked = $revoked;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): User
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getPropertySearchSettings(): array
    {
        return $this->propertySearchSettings;
    }

    public function setPropertySearchSettings(array $propertySearchSettings): User
    {
        $this->propertySearchSettings = $propertySearchSettings;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPassword()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getSalt()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials(): void
    {
    }

    public function hasAccessTokenExpired(): bool
    {
        return $this->accessTokenExpiresAt <= new DateTime();
    }
}
