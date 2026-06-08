<?php

namespace BMS\Models;

class EventMember extends Model
{
    protected string $table = 'event_members';

    public function createForEvent(int $eventId, int $memberId): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (event_id, member_id, joined_at)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([
            $eventId,
            $memberId,
            date('Y-m-d H:i:s')
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function createBulkForEvent(int $eventId, array $memberIds): array
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (event_id, member_id, joined_at)
            VALUES (?, ?, ?)
        ");

        $joinedAt = date('Y-m-d H:i:s');
        foreach ($memberIds as $memberId) {
            if (!$this->exists($eventId, (int) $memberId)) {
                $stmt->execute([
                    $eventId,
                    (int) $memberId,
                    $joinedAt
                ]);
            }
        }

        return $this->getByEventId($eventId);
    }

    public function getByEventId(int $eventId): array
    {
        $stmt = $this->db->prepare("
            SELECT em.*, m.id as member_id, m.name, m.nickname, m.stage_name, m.email, m.phone, m.role, m.section, m.avatar, m.status as member_status
            FROM {$this->table} em
            JOIN members m ON m.id = em.member_id
            WHERE em.event_id = ?
        ");

        $stmt->execute([$eventId]);
        return $stmt->fetchAll();
    }

    public function countByEventId(int $eventId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total
            FROM {$this->table}
            WHERE event_id = ?
        ");

        $stmt->execute([$eventId]);
        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    }

    public function exists(int $eventId, int $memberId): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total
            FROM {$this->table}
            WHERE event_id = ? AND member_id = ?
        ");

        $stmt->execute([$eventId, $memberId]);
        $row = $stmt->fetch();
        return ((int) ($row['total'] ?? 0)) > 0;
    }
}
