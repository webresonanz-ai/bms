<?php

namespace BMS\Models;

class Event extends Model
{
    protected string $table = 'events';

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (title, date, time, location, type, status, participants) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['title'],
            $data['date'],
            $data['time'],
            $data['location'],
            $data['type'],
            $data['status'] ?? 'upcoming',
            $data['participants'] ?? 0
        ]);
        
        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        return parent::delete($id);
    }

    public function getUpcoming(): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE status = 'upcoming' 
            ORDER BY date ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}