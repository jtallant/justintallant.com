<?php

namespace JustinTallant\Comments;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Factory;
use JustinTallant\Comments\CommentsRepository;
use Laravel\Lumen\Routing\Controller as BaseController;

class CommentsController extends BaseController
{
    private $comments;
    private $validator;

    public function __construct(CommentsRepository $comments, Factory $validator)
    {
        $this->comments = $comments;
        $this->validator = $validator;
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->comments->getEntryComments($request->get('entry_uri'))
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validator = $this->validator->make($request->all(), [
            'entry_uri' => 'required|string',
            'author' => 'required|string|max:70',
            'content' => 'required|string|max:1200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $data = $this->indicateBlogAuthorIfBlogAuthor($data);

        $newComment = $this->comments->createEntryComment($data);

        return response()
            ->json([
                'message' => 'Comment added successfully',
                'data' => $newComment,
            ], 201);
    }

    private function indicateBlogAuthorIfBlogAuthor(array $data): array
    {
        $data['is_author'] = false;

        $isBlogAuthor = $data['author']  === config('comments.author_secret');

        if ($isBlogAuthor) {
            return array_merge($data, [
                'is_author' => true,
                'author' => config('comments.author_name'),
            ]);
        }

        return $data;
    }
}