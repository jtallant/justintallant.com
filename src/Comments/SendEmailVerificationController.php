<?php

namespace JustinTallant\Comments;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Factory as ValidatorFactory;
use Laravel\Lumen\Routing\Controller as BaseController;

class EmailVerificationController extends BaseController
{
    private $validator;

    public function __construct(ValidatorFactory $validator)
    {
        $this->validator = $validator;
    }

    public function show(Request $request)
    {
        $validator = $this->validator->make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return new JsonResponse([
                'error' => 'Email address is required and must be valid'
            ], 400);
        }

        $emailAddress = $request->input('email');

        $this->sendEmail($emailAddress);

        return new Response('Verification email sent', 200);
    }

    private function sendEmail(string $emailAddress): void
    {
        return;
    }
}
