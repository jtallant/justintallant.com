<?php

namespace JustinTallant\Comments;

use Illuminate\Http\Request;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Doctrine\Persistence\ObjectRepository;
use JustinTallant\Comments\Entities\Email;
use Laravel\Lumen\Routing\Controller as BaseController;
use LaravelDoctrine\ORM\IlluminateRegistry as Registry;

class VerifyCommentsTokenController extends BaseController
{
    private EntityManager $em;
    private ObjectRepository $emails;

    public function __construct(Registry $registry)
    {
        $this->em = $registry->getManager('comments');
        $this->emails = $this->em->getRepository(Email::class);
    }

    public function show(Request $request)
    {
        $token = $request->input('token');

        $this->emails->findOneBy(['token' => $token]);

        if (!$token) {
            return new JsonResponse(['error' => 'No token found'], 400);
        }

        return new JsonResponse([
            'message' => 'Token is valid',
        ], 200);
    }
}
