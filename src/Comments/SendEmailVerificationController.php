<?php

namespace JustinTallant\Comments;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Factory as ValidatorFactory;
use Laravel\Lumen\Routing\Controller as BaseController;

class SendEmailVerificationController extends BaseController
{
    private $validator;
    private $mailer;

    public function __construct(ValidatorFactory $validator, MailerInterface $mailer)
    {
        $this->validator = $validator;
        $this->mailer = $mailer;
    }

    public function store(Request $request)
    {
        $validator = $this->validator->make($request->all(), [
            'email' => 'required|email',
            'entry_uri' => 'required',
        ]);

        if ($validator->fails()) {
            return new JsonResponse([
                'error' => 'Email address is required and must be valid'
            ], 400);
        }

        // we need to create an email record here

        $this->mailer->send(
            $request->input('email'),
            config('comments.mail_subject'),
            $this->body()
        );

        return new JsonResponse([
            'message' => 'Verification email sent',
            'status' => 'success'
        ], 200);
    }

    private function body()
    {
        // This needs to include a link for them to verify their email
        // the link needs to include a token that we can verify the user's email with
        return 'content here';
    }
}
