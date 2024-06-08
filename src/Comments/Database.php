<?php

namespace JustinTallant\Comments;

use PDO;
use PDOException;

class Database
{
    private $pdo;

    public function __construct(array $config)
    {
        try {
            $this->pdo = new PDO("sqlite:{$config['database']}");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->initializeSchema();
        } catch (PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->pdo;
    }

    private function initializeSchema()
    {
        $query = "
            CREATE TABLE IF NOT EXISTS comments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                entry_uri TEXT NOT NULL,
                author TEXT NOT NULL,
                content TEXT NOT NULL,
                is_author INTEGER NOT NULL DEFAULT 0,
                created_at TEXT NOT NULL
            );
        ";

        $this->pdo->exec($query);
    }
}
