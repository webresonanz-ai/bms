<?php

namespace BMS\Models;

class Member extends Model
{
    protected string $table = 'members';

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table}
            (name, nickname, email, stage_name, phone, role, section, birth_place, birth_date, domicile, join_date, status, performances, avatar, year_join, field_of_work)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['name'],
            $data['nickname'] ?? null,
            $data['email'],
            $data['stage_name'] ?? null,
            $data['phone'] ?? null,
            $data['role'] ?? 'Member',
            $data['section'],
            $data['birth_place'] ?? null,
            $data['birth_date'] ?? null,
            $data['domicile'] ?? null,
            $data['join_date'] ?? date('Y-m-d'),
            $data['status'] ?? 'active',
            $data['performances'] ?? 0,
            $data['avatar'] ?? null,
            $data['year_join'] ?? null,
            $data['field_of_work'] ?? null
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updateStatus(int $id, string $status): bool
    {
        return $this->update($id, ['status' => $status]);
    }

    public function getActive(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE status = 'active'");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getBySection(): array
    {
        $stmt = $this->db->prepare("
            SELECT section, GROUP_CONCAT(id) as ids 
            FROM {$this->table} 
            GROUP BY section
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}