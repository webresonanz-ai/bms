<?php

namespace BMS\Models;

use BMS\Database;
use PDO;

abstract class Model
{
    protected PDO $db;
    protected string $table;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function update(int $id, array $data): bool
    {
        $fields = array_keys($data);
        $placeholders = array_map(fn($field) => "{$field} = ?", $fields);
        $values = array_values($data);
        $values[] = $id;
        
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET " . implode(', ', $placeholders) . " 
            WHERE id = ?
        ");
        
        return $stmt->execute($values);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}