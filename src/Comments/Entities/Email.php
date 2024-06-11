<?php

declare(strict_types=1);

namespace JustinTallant\Comments\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="emails")
 */
class Email implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $entryUri;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $verifiedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expiresAt;

    public function __construct(string $name, string $email, string $entryUri)
    {
        $this->name = $name;
        $this->email = $email;
        $this->entryUri = $entryUri;
        $this->token = bin2hex(random_bytes(16));
        $this->createdAt = new \DateTime();
        $this->expiresAt = (new \DateTime())->modify('+3 days');
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): ?string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function entryUri(): string
    {
        return $this->entryUri;
    }

    public function token(): ?string
    {
        return $this->token;
    }

    public function verifiedAt(): ?\DateTime
    {
        return $this->verifiedAt;
    }

    public function verify(): void
    {
        $this->verifiedAt = new \DateTime();
    }

    public function verified(): bool
    {
        return $this->verifiedAt instanceof \DateTime;
    }

    public function createdAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function expiresAt(): ?\DateTime
    {
        return $this->expiresAt;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'expiresAt' => $this->expiresAt->format('Y-m-d H:i:s'),
        ];
    }
}
