<?php

namespace JustinTallant\Tests;

use JustinTallant\Comments\Entities\Comment;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected $em;
    protected $comments;

    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->em = app('registry')->getManager('comments');
        $this->comments = $this->em->getRepository(Comment::class);

        $this->rebuildDatabase();
    }

    protected function rebuildDatabase()
    {
        # Truncate the database
        $connection = $this->em->getConnection();
        $schemaManager = $connection->getSchemaManager();
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $classes = $this->em->getMetadataFactory()->getAllMetadata();

        foreach ($schemaManager->listTableNames() as $tableName) {
            $schemaManager->dropTable($tableName);
        }

        # Recreate the schema
        $schemaTool->createSchema($classes);
    }
}

