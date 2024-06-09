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

    public function getId(): int
    {
        return $this->id;
    }

    public function getEntryUri(): string
    {
        return $this->entryUri;
    }

    public function setEntryUri(string $entryUri): void
    {
        $this->entryUri = $entryUri;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getIsAuthor(): bool
    {
        return $this->isAuthor;
    }

    public function setIsAuthor(bool $isAuthor): void
    {
        $this->isAuthor = $isAuthor;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}

