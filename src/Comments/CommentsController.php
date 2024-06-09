<?php

namespace JustinTallant\Comments;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Factory;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment as TwigEnvironment;
use JustinTallant\Comments\CommentsRepository;
use Laravel\Lumen\Routing\Controller as BaseController;
use LaravelDoctrine\ORM\IlluminateRegistry as Registery;

class CommentsController extends BaseController
{
    private $validator;
    private $twig;
    private $em;

    public function __construct(Factory $validator, TwigEnvironment $twig, Registery $registry)
    {
        $this->validator = $validator;
        $this->twig = $twig;
        $this->em = $registry->getManager('comments')
                            ->getRepository(Entities\Comment::class);
    }

    public function index(Request $request, EntityManagerInterface $em): JsonResponse
    {
        return response()->json(
            $this->em->findBy(['entryUri' => $request->get('entry_uri')])
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

        $data = $this->indicateBlogAuthorIfBlogAuthor($data);

        $newComment = $this->comments->createEntryComment($data);

        return response()
            ->json([
                'message' => 'Comment added successfully',
                'data' => $newComment,
            ], 201);
    }

    private function renderCommentHtml(array $commentData): string
    {
        $loader = new \Twig\Loader\FilesystemLoader('/path/to/templates');
        $twig = new \Twig\Environment($loader);
        return $twig->render('partials/comment.twig', ['comment' => $commentData]);
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