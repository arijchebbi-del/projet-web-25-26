<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config\Database;
use App\Http\Request;
use App\Http\Response;
use App\Middleware\AuthRequired;
use PDO;

final class PostsController
{
    public static function create(): void
    {
        $userId = AuthRequired::userId();
        $body = Request::json();
        $content = trim((string) ($body['content'] ?? ''));
        $content = substr($content, 0, 60000);

        if ($content === '') {
            Response::json(['ok' => false, 'error' => 'VALIDATION_ERROR', 'message' => 'Post content is required.'], 422);
            return;
        }

        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO posts (user_id, content) VALUES (:user_id, :content)');
        $stmt->execute([
            'user_id' => $userId,
            'content' => $content,
        ]);

        Response::json([
            'ok' => true,
            'message' => 'Post created successfully.',
            'id' => (int) $pdo->lastInsertId(),
        ], 201);
    }

    public static function index(): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->query(
            'SELECT p.id, p.content, p.created_at, u.id AS author_id, i.prenom AS author_prenom, i.nom AS author_nom, u.avatar_url AS author_avatar
             FROM posts p
             INNER JOIN users u ON u.id = p.user_id
             INNER JOIN insatien i ON i.id = u.insatien_id
             ORDER BY p.created_at DESC
             LIMIT 50'
        );

        $posts = array_map(static fn (array $entry) => [
            'id' => (int) $entry['id'],
            'content' => $entry['content'],
            'createdAt' => $entry['created_at'],
            'author' => [
                'id' => (int) $entry['author_id'],
                'firstName' => $entry['author_prenom'],
                'lastName' => $entry['author_nom'],
                'avatarUrl' => $entry['author_avatar']
            ]
        ], $stmt->fetchAll());

        Response::json([
            'ok' => true,
            'data' => $posts,
        ]);
    }
}


