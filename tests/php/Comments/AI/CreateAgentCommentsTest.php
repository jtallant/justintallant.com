<?php

namespace Tests\Comments\AI;

use Mockery;
use JustinTallant\Tests\TestCase;
use Illuminate\Console\OutputStyle;
use JustinTallant\Comments\Entities\Comment;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\StringInput;
use JustinTallant\Comments\AI\CreateAgentComments;
use Symfony\Component\Console\Output\BufferedOutput;
use JustinTallant\Comments\AI\CommentWriterInterface;

class CreateAgentCommentsTest extends TestCase
{
    /** @test */
    public function it_creates_agent_comments_for_a_specified_comment_id()
    {
        $registry = $this->app->make('registry');

        $comment = new Comment(
            'entryUri',
            'authorName',
            'This is a generated comment'
        );

        $this->em->persist($comment);
        $this->em->flush();

        $prompts = [
            'Roaster1' => 'You are an intellectual with a sharp wit...',
            'NiceGuy17' => 'You are Mr. Nice Guy, an intelligent and incredibly kind...'
        ];

        $commentWriter = Mockery::mock(CommentWriterInterface::class);
        $commentWriter->shouldReceive('write')
            ->andReturn('Generated comment content');

        $createAgentComments = new CreateAgentComments($registry, $prompts);

        $input = new ArrayInput([
            'commentId' => $comment->id()
        ], $createAgentComments->getDefinition());

        $output = new BufferedOutput();
        $outputStyle = new OutputStyle(new StringInput(''), $output);

        $createAgentComments->setInput($input);
        $createAgentComments->setOutput($outputStyle);

        $createAgentComments->handle($commentWriter);

        $reply = $this->em->getRepository(Comment::class)->findOneBy(['repliesTo' => $comment]);

        $this->assertNotEmpty($reply, 'The comment should have replies.');
        $this->assertEquals($comment->id(), $reply->repliesTo()->id(), 'The reply should be linked to the original comment.');
        $this->assertContains($reply->author(), array_keys($prompts), 'The reply author should be either "Roast" or "nice_guy".');
    }
}
