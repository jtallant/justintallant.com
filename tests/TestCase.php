<?php

namespace JustinTallant\Tests;

use Illuminate\Support\Facades\Artisan;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->refreshApplication();

        // Create the comments table in the in-memory database
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('entry_uri', 255);
            $table->string('author', 255);
            $table->text('content');
            $table->timestamps();
        });

        // Create some comments in the in-memory database without using migrations
        DB::table('comments')->insert([
            [
                'entry_uri' => 'example-uri-1',
                'author' => 'Author One',
                'content' => 'This is the first comment.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'entry_uri' => 'example-uri-2',
                'author' => 'Author Two',
                'content' => 'This is the second comment.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'entry_uri' => 'example-uri-3',
                'author' => 'Author Three',
                'content' => 'This is the third comment.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Migrate the database
        // Artisan::call('migrate');

        // Seed the database
        // Artisan::call('db:seed');
    }

    public function tearDown(): void
    {
        // Artisan::call('migrate:reset');

        // parent::tearDown();
    }
}