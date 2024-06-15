<?php

declare(strict_types=1);

namespace JustinTallant\Comments\AI;

use Illuminate\Console\Command;
use JustinTallant\Comments\Entities\Comment;
use JustinTallant\Comments\AI\CommentWriterInterface;
use LaravelDoctrine\ORM\IlluminateRegistry as Registry;

class CreateAgentCommentReplies extends Command
{
    protected $signature = 'comments:create-agent-comment-replies {commentId?}';
    protected $description = 'Respond to a random number of new comments with AI';

    private $commentsManager;
    private $comments;
    private $prompts;

    public function __construct(Registry $registry, array $prompts)
    {
        parent::__construct();

        $commentsManager = $registry->getManager('comments');

        $this->commentsManager = $commentsManager;
        $this->comments = $commentsManager->getRepository(Comment::class);
        $this->prompts = $prompts;
    }

    public function handle(CommentWriterInterface $commentWriter): void
    {
        $this->respondToComments($commentWriter);
    }

    private function respondToComments(CommentWriterInterface $commentWriter): void
    {
        $commentId = $this->argument('commentId');
        $forReply = $this->commentsForReply($commentId);
        $promptCharacter = array_rand($this->prompts);
        $promptContent = $this->prompts[$promptCharacter];

        foreach ($forReply as $comment) {
            $replyContent = $commentWriter->write($promptContent, $comment->content());

            $reply = new Comment(
                $comment->entryUri(),
                $promptCharacter,
                $replyContent
            );

            $reply->setRepliesTo($comment);

            $this->commentsManager->persist($reply);
            $this->commentsManager->flush();
        }
    }

    private function commentsForReply($commentId = null, ?string $timeAgo = '-10 minutes'): array
    {
        if (!empty($commentId)) {
            return [$this->singleComment($commentId)];
        }

        $queryBuilder = $this->comments->createQueryBuilder('c')
            ->where('c.author NOT IN (:ignoredAuthors)')
            ->andWhere('c.repliesTo IS NULL')
            ->andWhere('c.createdAt >= :timeAgo')
            ->andWhere('LENGTH(c.content) >= 260')
            ->andWhere('(SELECT COUNT(r.id) FROM Comment r WHERE r.repliesTo = c.id) <= 30')
            ->setParameter('ignoredAuthors', $this->ignoredAuthors())
            ->setParameter('timeAgo', new \DateTime($timeAgo));

        return $queryBuilder->getQuery()->getResult();
    }

    private function singleComment($commentId): Comment
    {
        $comment = $this->comments->findOneBy(['id' => $commentId]);

        if (!$comment) {
            throw new \RuntimeException("Comment with ID $commentId not found");
        }

        return $comment;
    }

    private function ignoredAuthors(): array
    {
        return array_keys($this->prompts);
    }
}