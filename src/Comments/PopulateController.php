<?php

namespace JustinTallant\Comments;

use JustinTallant\Comments\Entities\Comment;
use Laravel\Lumen\Routing\Controller as BaseController;
use LaravelDoctrine\ORM\IlluminateRegistry as Registry;

class PopulateController extends BaseController
{
    private $em;
    private $comments;

    public function __construct(Registry $registry)
    {
        $this->em = $registry->getManager('comments');
        $this->comments = $this->em->getRepository(Comment::class);
    }

    public function show()
    {
        $comments = [
            [
                'entry-uri' => 'should-we-follow-srp-in-controllers',
                'author' => 'AssHat1',
                'content' => "Wow, this is one of the dumbest posts I've ever read. You clearly have no idea what you're talking about. Ignoring SRP in controllers is a recipe for disaster, and anyone with half a brain knows that. You're just promoting bad practices and laziness. Do everyone a favor and stop giving advice you clearly don't understand."
            ],
            [
                'entry-uri' => 'should-we-follow-srp-in-controllers',
                'author' => 'NiceGuy7',
                'content' => "Hey there! Great post! 😊 I totally agree with your take on SRP in controllers. Sometimes, sticking strictly to SRP can add unnecessary complexity, especially for smaller projects. Your method definitely reduces cognitive overhead by keeping everything in one place. I appreciate how you highlighted the balance between maintainability and simplicity. Your examples were clear and made it easy to see the benefits of both approaches. Keep up the awesome work! Looking forward to more of your insightful posts. 👍"
            ],
            [
                'entry-uri' => 'should-we-follow-srp-in-controllers',
                'author' => 'NiceGuy7',
                'content' => "Nice post."
            ]
        ];

        foreach ($comments as $commentData) {
            $comment = new Comment(
                $commentData['entry-uri'],
                $commentData['author'],
                $commentData['content']
            );

            $this->em->persist($comment);
        }

        $this->em->flush();
    }
}