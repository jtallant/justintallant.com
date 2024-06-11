<?php

declare(strict_types=1);

namespace JustinTallant\Comments;

use JustinTallant\Comments\Entities\Comment;

class CommentViewDecorator implements \JsonSerializable
{
    protected Comment $comment;

    /**
     * The application does not have authentication, so we need
     * to know when the site owner is the commentor in order
     * to display the site owners custom photo next to their name.
     */
    protected string $siteOwnerSecret;

    protected string $siteOwnerName;

    public function __construct(
        Comment $comment,
        string $siteOwnerSecret,
        string $siteOwnerName
    ) {
        $this->comment = $comment;
        $this->siteOwnerSecret = $siteOwnerSecret;
        $this->siteOwnerName = $siteOwnerName;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id(),
            'replies_to_id' => $this->repliesToId(),
            'entry_uri' => $this->comment->entryUri(),
            'author' => $this->displayName(),
            'content' => $this->content(),
            'created_at' => $this->date(),
        ];
    }

    public function __call($method, $arguments)
    {
        return $this->comment->$method(...$arguments);
    }

    public function id(): string
    {
        return (string) $this->comment->id();
    }

    public function repliesToId(): ?string
    {
        if (empty($this->comment->repliesTo())) {
            return null;
        }

        return (string) $this->comment->repliesTo()->id();
    }

    public function date(): string
    {
        return $this->comment->createdAt()->format('M jS g:ia');
    }

    public function content(): string
    {
        return strip_tags($this->comment->content(), '<br>');
    }

    public function isSiteOwner(): bool
    {
        return $this->comment->author() === $this->siteOwnerSecret;
    }

    public function displayName(): string
    {
        return $this->isSiteOwner()
            ? $this->siteOwnerName
            : $this->comment->author();
    }
}
