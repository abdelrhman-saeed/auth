<?php

namespace AbdelrhmanSaeed\Auth\Entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[ORM\Entity]
#[ORM\Table('refresh_tokens')]
#[UniqueConstraint(name: 'token_id', fields: ['token'])]
class RefreshToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    public function __construct(
        #[ORM\Column]
        private ?int $user_id,

        #[ORM\Column]
        private \DateTime $created_at,

        #[ORM\Column]
        private \DateTime $expires_at,

        #[ORM\Column]
        private string $token )
    {
    }
    /**
     * Get the value of id
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of user_id
     *
     * @return ?int
     */
    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    /**
     * Set the value of user_id
     *
     * @param ?int $user_id
     *
     * @return self
     */
    public function setUserId(?int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Get the value of created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    /**
     * Set the value of created_at
     *
     * @param \DateTime $created_at
     *
     * @return self
     */
    public function setCreatedAt(\DateTime $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Get the value of expires_at
     *
     * @return \DateTime
     */
    public function getExpiresAt(): \DateTime
    {
        return $this->expires_at;
    }

    /**
     * Set the value of expires_at
     *
     * @param \DateTime $expires_at
     *
     * @return self
     */
    public function setExpiresAt(\DateTime $expires_at): self
    {
        $this->expires_at = $expires_at;

        return $this;
    }

    /**
     * Get the value of token
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Set the value of token
     *
     * @param string $token
     *
     * @return self
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }
}