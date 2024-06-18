<?php

declare(strict_types=1);

namespace JustinTallant\Comments;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use JustinTallant\Comments\Entities\Email;
use Laravel\Lumen\Routing\Controller as BaseController;
use LaravelDoctrine\ORM\IlluminateRegistry as Registry;

class EmailVerificationController extends BaseController
{
    private ObjectManager $em;
    private ObjectRepository $emails;

    public function __construct(Registry $registry)
    {
        $this->em = $registry->getManager('comments');
        $this->emails = $this->em->getRepository(Email::class);
    }

    public function show(Request $request): View
    {
        $token = $request->input('token');

        $email = $this->emails->findOneBy(['token' => $token]);

        if (empty($email)) {
            return view('invalid-token');
        }

        $email->verify();
        $this->em->flush();

        return view('email-verified', ['email' => $email]);
    }
}
