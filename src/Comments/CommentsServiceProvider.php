<?php

declare(strict_types=1);

namespace JustinTallant\Comments;

use Illuminate\Support\ServiceProvider;
use JustinTallant\Comments\Entities\Comment;

class CommentsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadAndMergeConfigFrom(__DIR__ . '/config/database.php', 'database');
        $this->loadAndMergeConfigFrom(__DIR__ . '/config/doctrine.php', 'doctrine');
        $this->loadAndMergeConfigFrom(__DIR__ . '/config/comments.php', 'comments');

        $twig = $this->app->get('twig');

        $comments = $this->app->make('registry')
            ->getManager('comments')
            ->getRepository(Comment::class);

        $twig->addFunction(new \Twig\TwigFunction('comments', function ($entryUri) use ($comments) {
            $comments = $this->app->make('registry')
                ->getManager('comments')
                ->getRepository(Comment::class);

            return $comments->findBy([
                'entryUri' => $entryUri,
                'parent' => null,
            ], ['createdAt' => 'DESC']);
        }));

        $twig->addFunction(new \Twig\TwigFunction('childComments', function ($parentId) use ($comments) {
            return $comments->findBy(['parent' => $parentId,], ['createdAt' => 'ASC']);
        }));

        $this->setupCommentsDatabase();
    }

    public function boot(): void
    {
        $router = $this->app->router;
        require __DIR__ . '/routes.php';
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
        if (! file_exists(base_path('database/comments.sqlite'))) {
            touch(base_path('database/comments.sqlite'));
        }

        $entityManager = $this->app->make('registry')->getManager('comments');
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
        $classes = $entityManager->getMetadataFactory()->getAllMetadata();

        $schemaManager = $entityManager->getConnection()->getSchemaManager();
        $tables = $schemaManager->listTableNames();

        if (empty($tables)) {
            $schemaTool->createSchema($classes);
        }
    }
}
