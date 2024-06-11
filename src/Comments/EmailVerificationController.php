<?php

namespace JustinTallant\Comments;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustinTallant\Comments\Entities\Email;
use Laravel\Lumen\Routing\Controller as BaseController;
use LaravelDoctrine\ORM\IlluminateRegistry as Registry;

class EmailVerificationController extends BaseController
{
    private $em;
    private $emails;

    public function __construct(Registry $registry)
    {
        $this->em = $registry->getManager('comments');
        $this->emails = $this->em->getRepository(Email::class);
    }

    public function show(Request $request)
    {
        $token = $request->input('token');

        $email = $this->emails->findOneBy(['token' => $token]);

        if (empty($email)) {
            return new Response('Invalid token', 400);
        }

        $email->verify();
        $this->em->flush();

        return view('email-verified', ['email' => $email]);
    }
}
