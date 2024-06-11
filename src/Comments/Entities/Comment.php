<?php

declare(strict_types=1);

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
     * @ORM\ManyToOne(targetEntity="Comment")
     * @ORM\JoinColumn(name="replies_to_id", referencedColumnName="id", nullable=true)
     */
    private $repliesTo;

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
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct(
        string $entryUri,
        string $author,
        string $content,
        \DateTime $createdAt
    ) {
        $this->entryUri = $entryUri;
        $this->author = $author;
        $this->content = $content;
        $this->createdAt = $createdAt;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'replies_to_id' => $this->repliesTo ? $this->repliesTo->id : null,
            'entry_uri' => $this->entryUri,
            'author' => $this->author,
            'content' => $this->content,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }

    public function repliesTo(): ?Comment
    {
        return $this->repliesTo;
    }

    public function setRepliesTo(?Comment $repliesTo): void
    {
        $this->repliesTo = $repliesTo;
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

    public function createdAt(): \DateTime
    {
        return $this->createdAt;
    }
}
