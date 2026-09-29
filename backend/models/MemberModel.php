<?php

/**
 * MemberModel — CRUD for the `members` table.
 * Batavia Madrigal Singers — Backend API
 */

require_once __DIR__ . '/../config/database.php';

class MemberModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function all(): array
    {
        return $this->db
            ->query('SELECT * FROM members ORDER BY section, name')
            ->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM members WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO members (id, name, nickname, email, stage_name, birth_place, birth_date, domicile, phone, year_join, field_of_work, role, section, join_date, status, performances, avatar_url, created_at, updated_at)
             VALUES (:id, :name, :nickname, :email, :stage_name, :birth_place, :birth_date, :domicile, :phone, :year_join, :field_of_work, :role, :section, :join_date, :status, :performances, :avatar_url, :created_at, :updated_at)'
        );
        $stmt->execute([
            ':id'               => 1,  // auto-assign starting from 1
            ':name'             => trim($data['name']),
            ':nickname'         => trim($data['nickname'] ?? ''),
            ':email'            => trim($data['email'] ?? ''),
            ':stage_name'       => trim($data['stage_name'] ?? ''),
            ':birth_place'      => trim($data['birth_place'] ?? ''),
            ':birth_date'       => $data['birth_date'] ?? null,
            ':domicile'         => trim($data['domicile'] ?? ''),
            ':phone'            => trim($data['phone'] ?? ''),
            ':year_join'        => $data['year_join'] ?? '',
            ':field_of_work'    => trim($data['field_of_work'] ?? ''),
            ':role'             => $data['role'] ?? 'Sopran',
            ':section'          => $data['section'] ?? '',
            ':join_date'        => $data['join_date'] ?? null,
            ':status'           => $data['status'] ?? 'active',
            ':performances'     => (int) ($data['performances'] ?? 0),
            ':avatar_url'       => $data['avatar_url'] ?? 'https://voca-land.sgp1.cdn.digitaloceanspaces.com/0/1757684222527/9465e2e8.jpg',
            ':created_at'       => date('Y-m-d H:i:s'),
            ':updated_at'       => date('Y-m-d H:i:s'),
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE members
             SET name = :name, nickname = :nickname,
                 email = :email, stage_name = :stage_name, birth_place = :birth_place,
                 birth_date = :birth_date, domicile = :domicile, phone = :phone,
                 year_join = :year_join, field_of_work = :field_of_work, role = :role,
                 section = :section, join_date = :join_date, status = :status,
                 performances = :performances, avatar_url = :avatar_url,
                 updated_at = :updated_at
             WHERE id = :id'
        );
        return $stmt->execute([
            ':id'               => $id,
            ':name'             => trim($data['name']),
            ':nickname'         => trim($data['nickname'] ?? ''),
            ':email'            => trim($data['email'] ?? ''),
            ':stage_name'       => trim($data['stage_name'] ?? ''),
            ':birth_place'      => trim($data['birth_place'] ?? ''),
            ':birth_date'       => $data['birth_date'] ?? null,
            ':domicile'         => trim($data['domicile'] ?? ''),
            ':phone'            => trim($data['phone'] ?? ''),
            ':year_join'        => $data['year_join'] ?? '',
            ':field_of_work'    => trim($data['field_of_work'] ?? ''),
            ':role'             => $data['role'] ?? 'Sopran',
            ':section'          => $data['section'] ?? '',
            ':join_date'        => $data['join_date'] ?? null,
            ':status'           => $data['status'] ?? 'active',
            ':performances'     => (int) ($data['performances'] ?? 0),
            ':avatar_url'       => $data['avatar_url'] ?? 'https://voca-land.sgp1.cdn.digitaloceanspaces.com/0/1757684222527/9465e2e8.jpg',
            ':updated_at'       => date('Y-m-d H:i:s'),
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM members WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
