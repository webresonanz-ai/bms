<?php

/**
 * GalleryModel — CRUD for the `gallery_items` table.
 * Batavia Madrigal Singers — Backend API
 */

require_once __DIR__ . '/../config/database.php';

class GalleryModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function all(): array
    {
        return $this->db
            ->query('SELECT * FROM gallery_items ORDER BY sort_order, id')
            ->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM gallery_items WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO gallery_items (title, category, icon, sort_order)
             VALUES (:title, :category, :icon, :sort_order)'
        );
        $stmt->execute([
            ':title'      => trim($data['title']),
            ':category'   => trim($data['category']),
            ':icon'       => trim($data['icon'] ?? 'bi-image'),
            ':sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE gallery_items
             SET title = :title, category = :category, icon = :icon, sort_order = :sort_order
             WHERE id = :id'
        );
        return $stmt->execute([
            ':title'      => trim($data['title']),
            ':category'   => trim($data['category']),
            ':icon'       => trim($data['icon'] ?? 'bi-image'),
            ':sort_order' => (int) ($data['sort_order'] ?? 0),
            ':id'         => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM gallery_items WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
