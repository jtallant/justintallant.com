<?php

use JustinTallant\Tests\TestCase;
use JustinTallant\Comments\AI\GptCommentWriter;
use JustinTallant\Comments\AI\CommentWriterInterface;
use JustinTallant\Comments\AI\CreateAgentCommentReplies;

class CommentWriterProviderTest extends TestCase
{
    /** @test */
    public function it_binds_gpt_comment_writer_to_comment_writer_interface()
    {
        $this->assertInstanceOf(
            GptCommentWriter::class,
            $this->app->make(CommentWriterInterface::class)
        );
    }

    /** @test */
    public function it_creates_agent_comments_as_singleton()
    {
        $instanceOne = $this->app->make(CreateAgentCommentReplies::class);
        $instanceTwo = $this->app->make(CreateAgentCommentReplies::class);

        $this->assertSame($instanceOne, $instanceTwo);
    }
}
