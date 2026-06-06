<?php

namespace BMS\Models;

class User extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (name, email, password, role, avatar, phone, location, specialization, bio, join_date, achievements) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['password'],
            $data['role'] ?? 'Member',
            $data['avatar'] ?? null,
            $data['phone'] ?? null,
            $data['location'] ?? null,
            $data['specialization'] ?? null,
            $data['bio'] ?? null,
            $data['join_date'] ?? date('Y-m-d'),
            json_encode($data['achievements'] ?? [])
        ]);
        
        return (int) $this->db->lastInsertId();
    }
}