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
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private string $email;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private string $googleId;

    /**
     * @ORM\Column(type="string")
     */
    private string $refreshToken;

    /**
     * @ORM\Column(type="string")
     */
    private string $accessToken;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $accessTokenCreatedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $accessTokenExpiresAt;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $avatar;

    /**
     * @ORM\Column(type="json")
     */
    private array $propertySearchSettings = [];

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $revokedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $createdAt;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

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

    public function getAccessTokenCreatedAt(): DateTime
    {
        return $this->accessTokenCreatedAt;
    }

    public function setAccessTokenCreatedAt(DateTime $accessTokenCreatedAt): User
    {
        $this->accessTokenCreatedAt = $accessTokenCreatedAt;

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

    public function getRevokedAt(): ?DateTime
    {
        return $this->revokedAt;
    }

    public function setRevokedAt(?DateTime $revokedAt): User
    {
        $this->revokedAt = $revokedAt;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): User
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isRevoked(): bool
    {
        return null !== $this->revokedAt;
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
}
