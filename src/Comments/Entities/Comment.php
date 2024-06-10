<?php

namespace JustinTallant\Comments\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="comments", schema="")
 */
class Comment implements \JsonSerializable
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
    private $entryUri;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $author;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $isAuthor = false;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct(
        string $entryUri,
        string $author,
        string $content,
        \DateTime $createdAt,
        bool $isAuthor = false
    ) {
        $this->entryUri = $entryUri;
        $this->author = $author;
        $this->content = $content;
        $this->createdAt = $createdAt;
        $this->isAuthor = $isAuthor;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'entry_uri' => $this->entryUri,
            'author' => $this->author,
            'content' => $this->content,
            'is_author' => $this->isAuthor,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }

    public function id(): int
    {
        return $this->id;
    }

    public function entryUri(): string
    {
        return $this->entryUri;
    }

    public function author(): string
    {
        return $this->author;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function isAuthor(): bool
    {
        return $this->isAuthor;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
        $this->isAuthor = true;
    }

    public function createdAt(): \DateTime
    {
        return $this->createdAt;
    }
}