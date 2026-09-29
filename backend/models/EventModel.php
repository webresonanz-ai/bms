<?php

/**
 * EventModel — CRUD for the `events` table.
 * Batavia Madrigal Singers — Backend API
 */

require_once __DIR__ . '/../config/database.php';

class EventModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function all(): array
    {
        return $this->db
            ->query('SELECT * FROM events ORDER BY date DESC')
            ->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM events WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO events (title, date, time, venue, city, description, status)
             VALUES (:title, :date, :time, :venue, :city, :description, :status)'
        );
        $stmt->execute([
            ':title'       => trim($data['title']),
            ':date'        => $data['date'],
            ':time'        => $data['time'],
            ':venue'       => trim($data['venue']),
            ':city'        => trim($data['city']),
            ':description' => trim($data['description']),
            ':status'      => $data['status'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE events
             SET title = :title, date = :date, time = :time, venue = :venue,
                 city = :city, description = :description, status = :status
             WHERE id = :id'
        );
        return $stmt->execute([
            ':title'       => trim($data['title']),
            ':date'        => $data['date'],
            ':time'        => $data['time'],
            ':venue'       => trim($data['venue']),
            ':city'        => trim($data['city']),
            ':description' => trim($data['description']),
            ':status'      => $data['status'],
            ':id'          => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM events WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
