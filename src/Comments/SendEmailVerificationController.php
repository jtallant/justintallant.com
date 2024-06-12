<?php

namespace JustinTallant\Comments;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use JustinTallant\Comments\Entities\Email;
use Illuminate\Validation\Factory as ValidatorFactory;
use Laravel\Lumen\Routing\Controller as BaseController;
use LaravelDoctrine\ORM\IlluminateRegistry as Registry;

class SendEmailVerificationController extends BaseController
{
    private $validator;
    private $mailer;
    private $em;
    private $emails;

    public function __construct(
        ValidatorFactory $validator,
        MailerInterface $mailer,
        Registry $registry
    ) {
        $this->validator = $validator;
        $this->mailer = $mailer;
        $this->em = $registry->getManager('comments');
        $this->emails = $this->em->getRepository(Email::class);
    }

    public function store(Request $request)
    {
        $validator = $this->validator->make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'entry_uri' => 'required',
        ]);

        if ($validator->fails()) {
            return new JsonResponse([
                'error' => $validator->errors()->first()
            ], 400);
        }

        $data = [
            'name' => strip_tags($request->input('name')),
            'email' => filter_var($request->input('email'), FILTER_SANITIZE_EMAIL),
            'entry_uri' => strip_tags($request->input('entry_uri')),
        ];

        $email = $this->getEmail($data);

        $this->em->persist($email);
        $this->em->flush();

        $this->mailer->send(
            $request->input('email'),
            config('comments.mail_subject'),
            $this->body($email)
        );

        return new JsonResponse([
            'message' => 'Verification email sent',
            'status' => 'success'
        ], 200);
    }

    private function getEmail(array $data): Email
    {
        $existingEmail = $this->emails->findOneBy(['email' => $data['email']]);

        if ($existingEmail) {
            $existingEmail->updateName($data['name']);
            $existingEmail->resetToken();

            return $existingEmail;
        }

        return new Email(
            $data['name'],
            $data['email'],
            $data['entry_uri']
        );
    }

    private function body(Email $email): string
    {
        $url = url('/comments/email-verification');
        $name = $email->name();
        $verificationLink = $url . '?token=' . $email->token();

        return <<<EOT
        Hey $name,

        Thanks for checking out my blog. I'm looking forward to your comment contributions!
        Please just click the link below to verify your email. Your data will be stored in
        the browser's local storage instead of a cookie and there is no authentication
        (sign in) on my blog so you are only validated with the browser you are using now
        and if you clear your storage data on your browser, you'll have to verify again.
        Just click the link below to get commenting.

        The link below will expire in a few days.
        $verificationLink

        Best,
        Justin
        EOT;
    }
}
