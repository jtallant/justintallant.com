<?php

declare(strict_types=1);

namespace JustinTallant\Comments;

use Illuminate\Support\ServiceProvider;
use JustinTallant\Comments\Entities\Comment;
use JustinTallant\Comments\CommentViewDecorator;

class CommentsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigs();

        $this->loadCommentTemplates();

        $this->addCommentsTwigFunctions();

        $this->setupCommentsDatabase();
    }

    public function boot(): void
    {
        $router = $this->app->router;
        require __DIR__ . '/routes.php';
    }

    private function mergeConfigs()
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
        if (! file_exists(base_path('database/comments.sqlite'))) {
            touch(base_path('database/comments.sqlite'));
        }

        $entityManager = $this->app->make('registry')->getManager('comments');
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
        $classes = $entityManager->getMetadataFactory()->getAllMetadata();

        $schemaManager = $entityManager->getConnection()->getSchemaManager();
        $tables = $schemaManager->listTableNames();

        if (['comments', 'emails'] != $tables) {
            $schemaTool->createSchema($classes);
        }
    }

    private function addCommentsTwigFunctions(): void
    {
        $twig = $this->app->get('twig');

        $commentsRepo = $this->app->make('registry')
            ->getManager('comments')
            ->getRepository(Comment::class);

        $config = $this->app->make('config');
        $siteOwnerSecret = $config->get('comments.site_owner_secret');
        $siteOwnerName = $config->get('comments.site_owner_name');

        $getComments = function ($entryUri) use ($commentsRepo, $siteOwnerSecret, $siteOwnerName) {
            $comments = $commentsRepo->findBy([
                'entryUri' => $entryUri,
                'repliesTo' => null,
            ], ['createdAt' => 'DESC']);

            return array_map(function ($comment) use ($siteOwnerSecret, $siteOwnerName) {
                return new CommentViewDecorator($comment, $siteOwnerSecret, $siteOwnerName);
            }, $comments);
        };

        $getChildren = function ($repliesToId) use ($commentsRepo, $siteOwnerSecret, $siteOwnerName) {
            $comments =  $commentsRepo->findBy(['repliesTo' => $repliesToId,], ['createdAt' => 'ASC']);

            return array_map(function ($comment) use ($siteOwnerSecret, $siteOwnerName) {
                return new CommentViewDecorator($comment, $siteOwnerSecret, $siteOwnerName);
            }, $comments);
        };

        $twig->addFunction(new \Twig\TwigFunction('comments', $getComments));
        $twig->addFunction(new \Twig\TwigFunction('childComments', $getChildren));
    }

    private function loadCommentTemplates(): void
    {
        $viewFactory = $this->app->get('view');
        $newPath = base_path('src/Comments/templates');
        $viewFactory->addLocation($newPath);
    }
}
