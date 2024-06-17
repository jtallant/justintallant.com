<?php

declare(strict_types=1);

namespace JustinTallant\Comments;

use Doctrine\ORM\Tools\SchemaTool;
use Illuminate\Support\ServiceProvider;

class CommentsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigs();
        $this->setupCommentsDatabase();
    }

    public function boot(): void
    {
        /** @phpstan-ignore-next-line */
        $router = $this->app->router;
        require __DIR__ . '/routes.php';
    }

    private function mergeConfigs(): void
    {
        $this->loadAndMergeConfigFrom(__DIR__ . '/config/database.php', 'database');
        $this->loadAndMergeConfigFrom(__DIR__ . '/config/doctrine.php', 'doctrine');
        $this->loadAndMergeConfigFrom(__DIR__ . '/config/comments.php', 'comments');
    }

    private function loadAndMergeConfigFrom(string $path, string $key): void
    {
        $config = $this->app->make('config');
        $original = $config->get($key, []);
        $values = require $path;
        $config->set($key, array_merge_recursive($original, $values));
    }

    private function setupCommentsDatabase(): void
    {
        if (php_sapi_name() !== 'cli' && ! file_exists(base_path('database/comments.sqlite'))) {
            touch(base_path('database/comments.sqlite'));
        }

        $entityManager = $this->app->make('registry')->getManager('comments');
        $schemaTool = new SchemaTool($entityManager);
        $classes = $entityManager->getMetadataFactory()->getAllMetadata();

        $schemaManager = $entityManager->getConnection()->getSchemaManager();
        $tables = $schemaManager->listTableNames();

        if (['comments', 'emails'] != $tables) {
            $schemaTool->createSchema($classes);
        }
    }
}