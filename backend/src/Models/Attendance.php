<?php

namespace BMS\Models;

class Attendance extends Model
{
    protected string $table = 'attendance';

    public function createOrUpdate(int $eventId, int $memberId, array $data): array
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (event_id, member_id, status, notes, recorded_at)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            status = VALUES(status),
            notes = VALUES(notes),
            recorded_at = VALUES(recorded_at)
        ");

        $stmt->execute([
            $eventId,
            $memberId,
            $data['status'],
            $data['notes'] ?? null,
            $data['recorded_at'] ?? date('Y-m-d H:i:s')
        ]);

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE event_id = ? AND member_id = ?");
        $stmt->execute([$eventId, $memberId]);
        return $stmt->fetch() ?: [];
    }

    public function updateStatus(int $eventId, int $memberId, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET status = ?, notes = ?, recorded_at = ?
            WHERE event_id = ? AND member_id = ?
        ");

        return $stmt->execute([
            $data['status'],
            $data['notes'] ?? null,
            $data['recorded_at'] ?? date('Y-m-d H:i:s'),
            $eventId,
            $memberId
        ]);
    }

    public function getByEventId(int $eventId): array
    {
        $stmt = $this->db->prepare("
            SELECT a.*, m.id as member_id, m.name, m.email, m.role, m.section, m.avatar
            FROM {$this->table} a
            JOIN members m ON m.id = a.member_id
            WHERE a.event_id = ?
        ");

        $stmt->execute([$eventId]);
        return $stmt->fetchAll();
    }

    public function findOne(int $eventId, int $memberId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE event_id = ? AND member_id = ?
        ");

        $stmt->execute([$eventId, $memberId]);
        return $stmt->fetch() ?: null;
    }

    public function getStatistics(int $eventId): array
    {
        $presentStmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE event_id = ? AND status = 'Present'");
        $lateStmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE event_id = ? AND status = 'Late'");
        $absentStmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE event_id = ? AND status = 'Absent'");
        $excusedStmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE event_id = ? AND status = 'Excused'");

        $presentStmt->execute([$eventId]);
        $lateStmt->execute([$eventId]);
        $absentStmt->execute([$eventId]);
        $excusedStmt->execute([$eventId]);

        return [
            'present_count' => (int) ($presentStmt->fetch()['total'] ?? 0),
            'late_count' => (int) ($lateStmt->fetch()['total'] ?? 0),
            'absent_count' => (int) ($absentStmt->fetch()['total'] ?? 0),
            'excused_count' => (int) ($excusedStmt->fetch()['total'] ?? 0)
        ];
    }
}
