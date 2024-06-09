<?php

namespace JustinTallant\Tests\Comments;

use JustinTallant\Tests\TestCase;

class CommentsControllerTest extends TestCase
{
    /**
     * Test index method returns a JSON response.
     */
    public function testIndexReturnsJson()
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
            $comment = new \JustinTallant\Comments\Entities\Comment();
            $comment->setEntryUri($data['entryUri']);
            $comment->setAuthor($data['author']);
            $comment->setContent($data['content']);
            $comment->setCreatedAt(new \DateTime());
            $this->em->persist($comment);
        }

        $this->em->flush();
        $response = $this->get('/api/comments?entry_uri=example-uri');
        $response->seeStatusCode(200);
        $response->seeJsonStructure([
            '*' => [
                'entry_uri', 'author', 'content' // Adjust fields based on actual JSON structure
            ]
        ]);
    }

    /**
     * Test store method for valid data.
     */
    public function testStoreValidData()
    {
        // $data = [
        //     'entry_uri' => 'example-uri',
        //     'author' => 'John Doe',
        //     'content' => 'This is a sample comment.'
        // ];

        // $response = $this->post('/comments', $data);
        // $response->seeStatusCode(201);
        // $response->seeJson([
        //     'message' => 'Comment added successfully'
        // ]);
    }

    /**
     * Test store method for invalid data.
     */
    public function testStoreInvalidData()
    {
        // $data = []; // Sending empty data to trigger validation errors

        // $response = $this->post('/comments', $data);
        // $response->seeStatusCode(422);
        // $response->seeJsonStructure([
        //     'message', 'errors'
        // ]);
    }
}
