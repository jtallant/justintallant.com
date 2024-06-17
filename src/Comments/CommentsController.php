<?php

declare(strict_types=1);

namespace JustinTallant\Comments;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Factory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use JustinTallant\Comments\Entities\Comment;
use Laravel\Lumen\Routing\Controller as BaseController;
use LaravelDoctrine\ORM\IlluminateRegistry as Registry;

class CommentsController extends BaseController
{
    private ObjectManager $em;
    private ObjectRepository $comments;
    private Factory $validator;

    public function __construct(Factory $validator, Registry $registry)
    {
        $this->em = $registry->getManager('comments');
        $this->comments = $this->em->getRepository(Comment::class);
        $this->validator = $validator;
    }

    public function index(Request $request): JsonResponse
    {
        $comments = $this->comments
            ->findBy([
                'entryUri' => $request->get('entry_uri')
            ]);

        $siteOwnerSecret = config('comments.site_owner_secret');
        $siteOwnerName = config('comments.site_owner_name');

        $comments = array_map(function ($comment) use ($siteOwnerSecret, $siteOwnerName) {
            return new CommentViewDecorator($comment, $siteOwnerSecret, $siteOwnerName);
        }, $comments);

        return new JsonResponse($comments);
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
            return new JsonResponse([
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
            $data['content']
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
            'data' => new CommentViewDecorator(
                $comment,
                config('comments.site_owner_secret'),
                config('comments.site_owner_name')
            ),
        ], 201);
    }
}