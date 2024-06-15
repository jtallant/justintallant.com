<?php

declare(strict_types=1);

namespace JustinTallant\Comments\AI;

use Skimpy\Repo\Entries;
use Illuminate\Console\Command;
use JustinTallant\Comments\Entities\Comment;
use JustinTallant\Comments\AI\CommentWriterInterface;
use LaravelDoctrine\ORM\IlluminateRegistry as Registry;

class CreateAgentEntryComments extends Command
{
    protected $signature = 'comments:create-agent-entry-comments {entryId?}';
    protected $description = 'Respond to entries with AI';

    private $commentsManager;
    private $entries;
    private $comments;
    private $prompts;

    public function __construct(Registry $registry, array $prompts, Entries $entries)
    {
        parent::__construct();

        $commentsManager = $registry->getManager('comments');

        $this->commentsManager = $commentsManager;
        $this->entries = $entries;
        $this->comments = $commentsManager->getRepository(Comment::class);
        $this->prompts = $prompts;
    }

    public function handle(CommentWriterInterface $writer): void
    {
        $this->respondToEntries($writer);
    }

    private function respondToEntries(CommentWriterInterface $writer): void
    {
        $excludeEntries = $this->entryUrisWithComments();

        $entries = $this->entries->createQueryBuilder('e')
            ->where('e.uri NOT IN (:excludeEntries)')
            ->setParameter('excludeEntries', $excludeEntries)
            ->getQuery()
            ->getResult();

        foreach ($this->prompts as $promptCharacter => $promptContent) {
            $this->writeComment($entries, $writer, $promptCharacter, $promptContent);
        }
    }

    private function writeComment(
        array $entries,
        CommentWriterInterface $writer,
        string $promptCharacter,
        string $promptContent
    ): void {
        foreach ($entries as $entry) {
            $commentContent = $writer->write($promptContent, $entry->getContent());
            $comment = new Comment($entry->getUri(), $promptCharacter, $commentContent);

            $this->commentsManager->persist($comment);
            $this->commentsManager->flush();
        }
    }

    /**
     * The URIs of entries that already have a bot comment based on the entry content
     */
    private function entryUrisWithComments(): array
    {
        $result = $this->comments->createQueryBuilder('c')
            ->select('c.entryUri')
            ->distinct()
            ->where('c.author IN (:ignoredAuthors)')
            ->setParameter('ignoredAuthors', $this->ignoredAuthors())
            ->getQuery()
            ->getResult();

        return array_column($result, 'entryUri');
    }

    private function ignoredAuthors(): array
    {
        return array_keys($this->prompts);
    }
}
