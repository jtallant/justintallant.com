<?php

declare(strict_types=1);

namespace JustinTallant\Comments;

use PDO;

class CommentsRepository
{
    private $pdo;

    public function __construct(Database $database)
    {
        $this->pdo = $database->getConnection();
    }

    public function getEntryComments(string $entryUri): array
    {
        $sql = <<<SQL
            SELECT * FROM comments
            WHERE entry_uri = :entry_uri
            ORDER BY created_at DESC
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['entry_uri' => $entryUri]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
    }

    public function createEntryComment(array $data): ?string
    {
        $sql = <<<SQL
            INSERT INTO comments (entry_uri, author, content, is_author, created_at)
            VALUES (:entry_uri, :author, :content, :is_author, :created_at)
        SQL;

        $this->pdo->prepare($sql)->execute([
            'entry_uri' => strip_tags($data['entry_uri']),
            'author' => strip_tags($data['author']),
            'content' => strip_tags($data['content']),
            'is_author' => $data['is_author'] ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $this->pdo->lastInsertId() ?? null;
    }
}
