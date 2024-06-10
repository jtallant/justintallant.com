<?php

namespace JustinTallant\Comments;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Factory;
use JustinTallant\Comments\Entities\Comment;
use Laravel\Lumen\Routing\Controller as BaseController;
use LaravelDoctrine\ORM\IlluminateRegistry as Registry;

class CommentsController extends BaseController
{
    private $validator;
    private $em;
    private $comments;

    public function __construct(Factory $validator, Registry $registry)
    {
        $this->validator = $validator;
        $this->em = $registry->getManager('comments');
        $this->comments = $this->em->getRepository(Comment::class);
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->comments->findBy(['entryUri' => $request->get('entry_uri')])
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validator = $this->validator->make($request->all(), [
            'entry_uri' => 'required|string',
            'author' => 'required|string|max:70',
            'content' => 'required|string|max:2400',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $data['content'] = nl2br(e($data['content']));
        $data['content'] = strip_tags($data['content'], '<br>');

        $comment = new Comment(
            $data['entry_uri'],
            $data['author'],
            $data['content'],
            new \DateTime()
        );

        $this->indicateBlogAuthorIfBlogAuthor($comment);

        $this->em->persist($comment);
        $this->em->flush();

        return response()->json([
            'message' => 'Comment added successfully',
            'data' => $comment,
        ], 201);
    }

    private function indicateBlogAuthorIfBlogAuthor(Comment $comment): void
    {
        if ($comment->author() === config('comments.author_secret')) {
            $comment->setAuthor(config('comments.author_name'));
        }
    }
}