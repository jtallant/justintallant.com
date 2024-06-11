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
    private $em;
    private $comments;
    private $validator;

    public function __construct(Factory $validator, Registry $registry)
    {
        $this->em = $registry->getManager('comments');
        $this->comments = $this->em->getRepository(Comment::class);
        $this->validator = $validator;
    }

    public function index(Request $request): JsonResponse
    {
        return new JsonResponse(
            $this->comments->findBy(['entryUri' => $request->get('entry_uri')])
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validator = $this->validator->make($request->all(), [
            'entry_uri' => 'required|string',
            'author' => 'required|string|max:70',
            'content' => 'required|string|max:2400',
            'replies_to_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $data['entry_uri'] = strip_tags($data['entry_uri']);
        $data['author'] = strip_tags($data['author']);
        $data['content'] = nl2br(e($data['content']));
        $data['content'] = strip_tags($data['content'], '<br>');

        $comment = new Comment(
            $data['entry_uri'],
            $data['author'],
            $data['content'],
            new \DateTime()
        );

        if (!empty($data['replies_to_id'])) {

            $repliesTo = $this->comments->find($data['replies_to_id']);

            if ($repliesTo) {
                $comment->setRepliesTo($repliesTo);
            }
        }

        $this->em->persist($comment);
        $this->em->flush();

        return new JsonResponse([
            'message' => 'Comment added successfully',
            'data' => $comment,
        ], 201);
    }
}