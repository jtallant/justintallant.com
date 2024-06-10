<?php

namespace JustinTallant\Tests\Comments;

use JustinTallant\Tests\TestCase;
use JustinTallant\Comments\Entities\Comment;

class CommentsControllerTest extends TestCase
{
    /** @test */
    public function index_of_entry_uri_returns_entry_comment_json()
    {
        $commentsData = [
            [
                'entryUri' => 'example-uri',
                'author' => 'John Doe',
                'content' => 'This is a sample comment.'
            ],
            [
                'entryUri' => 'example-uri',
                'author' => 'Jane Smith',
                'content' => 'This is another sample comment.'
            ]
        ];

        foreach ($commentsData as $data) {
            $comment = new Comment(
                $data['entryUri'],
                $data['author'],
                $data['content'],
                new \DateTime()
            );
            $this->em->persist($comment);
        }

        $this->em->flush();

        $response = $this->get('/api/comments?entry_uri=example-uri');
        $response->seeStatusCode(200);

        $response->seeJsonStructure([
            '*' => [
                'entry_uri', 'author', 'content', 'is_author', 'created_at',
            ]
        ]);
    }

    /** @test */
    public function validation_fails_if_missing_field()
    {
        $data = [
            'entry_uri' => 'example-uri',
            'author' => '',
            'content' => 'This is a sample comment.'
        ];

        $response = $this->post('/api/comments', $data);
        $response->seeStatusCode(422);
        $response->seeJsonStructure([
            'message',
            'errors' => [
                'author'
            ]
        ]);
    }

    /** @test */
    public function validation_fails_if_content_is_too_long()
    {
        $data = [
            'entry_uri' => 'example-uri',
            'author' => 'John Doe',
            'content' => str_repeat('a', 2401) // 2401 characters long
        ];

        $response = $this->post('/api/comments', $data);
        $response->seeStatusCode(422);
        $response->seeJsonStructure([
            'message',
            'errors' => [
                'content'
            ]
        ]);
    }

    /** @test */
    public function comment_is_successfully_created()
    {
        $data = [
            'entry_uri' => 'example-uri',
            'author' => 'John Doe',
            'content' => 'This is a sample comment.'
        ];

        $response = $this->post('/api/comments', $data);
        $response->seeStatusCode(201);
        $response->seeJsonStructure([
            'message',
            'data' => [
                'id',
                'entry_uri',
                'author',
                'content',
                'is_author',
                'created_at'
            ]
        ]);
    }

    /** @test */
    public function is_author_is_true_if_name_matches_author_secret_key()
    {
        config(['comments.author_secret' => 'authorsecret']);
        config(['comments.author_name' => 'Justin Tallant']);

        $data = [
            'entry_uri' => 'example-uri',
            'author' => 'authorsecret',
            'content' => 'This is a sample comment.'
        ];

        $response = $this->post('/api/comments', $data);

        $response->seeJsonContains([
            'is_author' => true,
            'author' => 'Justin Tallant',
            'content' => 'This is a sample comment.',
        ]);
    }
}
