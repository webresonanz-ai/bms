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
        $imageUrl  = $this->cleanImageUrl($data['image_url'] ?? null);
        $photoDate = $this->cleanDate($data['photo_date'] ?? null);

        try {
            $stmt = $this->db->prepare(
                'INSERT INTO gallery_items (title, category, icon, image_url, photo_date, sort_order)
                 VALUES (:title, :category, :icon, :image_url, :photo_date, :sort_order)'
            );
            $stmt->execute([
                ':title'      => trim($data['title']),
                ':category'   => trim($data['category']),
                ':icon'       => trim($data['icon'] ?? 'bi-image'),
                ':image_url'  => $imageUrl,
                ':photo_date' => $photoDate,
                ':sort_order' => (int) ($data['sort_order'] ?? 0),
            ]);
        } catch (PDOException $e) {
            // Fallback for DBs where migration 004 hasn't run yet
            if ($e->getCode() !== '42S22') {
                throw $e;
            }
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
        }
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $imageUrl  = $this->cleanImageUrl($data['image_url'] ?? null);
        $photoDate = $this->cleanDate($data['photo_date'] ?? null);

        try {
            $stmt = $this->db->prepare(
                'UPDATE gallery_items
                 SET title = :title, category = :category, icon = :icon,
                     image_url = :image_url, photo_date = :photo_date, sort_order = :sort_order
                 WHERE id = :id'
            );
            return $stmt->execute([
                ':title'      => trim($data['title']),
                ':category'   => trim($data['category']),
                ':icon'       => trim($data['icon'] ?? 'bi-image'),
                ':image_url'  => $imageUrl,
                ':photo_date' => $photoDate,
                ':sort_order' => (int) ($data['sort_order'] ?? 0),
                ':id'         => $id,
            ]);
        } catch (PDOException $e) {
            // Fallback for DBs where migration 004 hasn't run yet
            if ($e->getCode() !== '42S22') {
                throw $e;
            }
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
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM gallery_items WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Normalise an image URL/path for storage (NULL when empty, max 512 chars).
     */
    private function cleanImageUrl(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : '';
        if ($value === '') {
            return null;
        }
        return substr($value, 0, 512);
    }

    /**
     * Normalise a photo date for storage (YYYY-MM-DD or NULL).
     */
    private function cleanDate(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : '';
        if ($value === '') {
            return null;
        }
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value, $m)) {
            return null;
        }
        if (!checkdate((int) $m[2], (int) $m[3], (int) $m[1])) {
            return null;
        }
        return $value;
    }
}
