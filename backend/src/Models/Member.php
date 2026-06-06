<?php

namespace BMS\Models;

class Member extends Model
{
    protected string $table = 'members';

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (name, email, phone, role, section, join_date, status, performances, avatar) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['phone'] ?? null,
            $data['role'] ?? 'Member',
            $data['section'],
            $data['join_date'] ?? date('Y-m-d'),
            $data['status'] ?? 'active',
            $data['performances'] ?? 0,
            $data['avatar'] ?? null
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