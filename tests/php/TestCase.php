<?php

namespace JustinTallant\Tests;

use JustinTallant\Comments\Entities\Comment;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }

    public function setUp(): void
    {
        parent::setUp();
    }
}

