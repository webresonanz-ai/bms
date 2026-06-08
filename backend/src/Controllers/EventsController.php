<?php

namespace BMS\Controllers;

use BMS\Models\Event;
use BMS\Models\Member;
use BMS\Models\EventMember;
use BMS\Models\Attendance;

class EventsController
{
    private Event $eventModel;
    private Member $memberModel;
    private EventMember $eventMemberModel;
    private Attendance $attendanceModel;

    public function __construct()
    {
        $this->eventModel = new Event();
        $this->memberModel = new Member();
        $this->eventMemberModel = new EventMember();
        $this->attendanceModel = new Attendance();
    }

    public function index(): void
    {
        $events = $this->eventModel->all();
        $this->jsonResponse($this->formatEvents($events));
    }

    public function upcoming(): void
    {
        $events = $this->eventModel->getUpcoming();
        $this->jsonResponse($this->formatEvents($events));
    }

    public function store(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $id = $this->eventModel->create($data);
        $event = $this->eventModel->find($id);

        $this->jsonResponse($this->formatEvent($event), 201);
    }

    public function destroy(int $id): void
    {
        $this->eventModel->delete($id);
        $this->jsonResponse(['message' => 'Event deleted']);
    }

    public function update(int $id): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $event = $this->eventModel->find($id);
        if (!$event) {
            $this->jsonResponse(['error' => 'Event not found'], 404);
        }

        $this->eventModel->update($id, $data);
        $updatedEvent = $this->eventModel->find($id);

        $this->jsonResponse($this->formatEvent($updatedEvent));
    }

    public function participants(int $eventId): void
    {
        $event = $this->eventModel->find($eventId);
        if (!$event) {
            $this->jsonResponse(['error' => 'Event not found'], 404);
        }

        $members = $this->eventMemberModel->getByEventId($eventId);
        $formatted = array_map(function ($member) {
            return [
                'id' => (int) $member['member_id'],
                'member_id' => (int) $member['member_id'],
                'name' => $member['name'],
                'nickname' => $member['nickname'],
                'stage_name' => $member['stage_name'],
                'email' => $member['email'],
                'phone' => $member['phone'],
                'role' => $member['role'],
                'section' => $member['section'],
                'avatar' => $member['avatar'],
                'status' => $member['member_status'],
                'attendance' => null
            ];
        }, $members);

        $this->jsonResponse([
            'event' => $this->formatEvent($event),
            'members' => $formatted
        ]);
    }

    public function addParticipant(int $eventId): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $memberId = (int) ($data['member_id'] ?? 0);

        if (!$memberId) {
            $this->jsonResponse(['error' => 'member_id is required'], 400);
        }

        $event = $this->eventModel->find($eventId);
        if (!$event) {
            $this->jsonResponse(['error' => 'Event not found'], 404);
        }

        $member = $this->memberModel->find($memberId);
        if (!$member) {
            $this->jsonResponse(['error' => 'Member not found'], 404);
        }

        if ($this->eventMemberModel->exists($eventId, $memberId)) {
            $this->jsonResponse(['error' => 'Member already joined this event'], 400);
        }

        $this->eventMemberModel->createForEvent($eventId, $memberId);
        $this->updateEventParticipantsCount($eventId);

        $this->jsonResponse(['message' => 'Participant added'], 201);
    }

    public function addParticipants(int $eventId): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $memberIds = $data['member_ids'] ?? [];

        if (!is_array($memberIds) || empty($memberIds)) {
            $this->jsonResponse(['error' => 'member_ids array is required'], 400);
        }

        $event = $this->eventModel->find($eventId);
        if (!$event) {
            $this->jsonResponse(['error' => 'Event not found'], 404);
        }

        $this->eventMemberModel->createBulkForEvent($eventId, $memberIds);
        $this->updateEventParticipantsCount($eventId);

        $this->jsonResponse(['message' => 'Participants added', 'count' => count($memberIds)], 201);
    }

    private function updateEventParticipantsCount(int $eventId): void
    {
        $participants = $this->eventMemberModel->getByEventId($eventId);
        $count = count($participants);
        $this->eventModel->update($eventId, ['participants' => $count]);
    }

    public function bulkAttendance(int $eventId): void
    {
        $event = $this->eventModel->find($eventId);
        if (!$event) {
            $this->jsonResponse(['error' => 'Event not found'], 404);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $records = $data['records'] ?? [];

        if (!is_array($records)) {
            $this->jsonResponse(['error' => 'records must be an array'], 400);
        }

        foreach ($records as $record) {
            $this->attendanceModel->createOrUpdate(
                $eventId,
                (int) $record['member_id'],
                [
                    'status' => $record['status'],
                    'notes' => $record['notes'] ?? null,
                    'recorded_at' => $record['recorded_at'] ?? date('Y-m-d H:i:s')
                ]
            );
        }

        $attendance = $this->attendanceModel->getByEventId($eventId);
        $this->jsonResponse(['attendance' => $attendance]);
    }

    public function setAttendance(int $eventId, int $memberId): void
    {
        $event = $this->eventModel->find($eventId);
        if (!$event) {
            $this->jsonResponse(['error' => 'Event not found'], 404);
        }

        $member = $this->memberModel->find($memberId);
        if (!$member) {
            $this->jsonResponse(['error' => 'Member not found'], 404);
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $existing = $this->attendanceModel->findOne($eventId, $memberId);
        if ($existing) {
            $this->attendanceModel->updateStatus($eventId, $memberId, $data);
            $record = $this->attendanceModel->findOne($eventId, $memberId);
        } else {
            $record = $this->attendanceModel->createOrUpdate($eventId, $memberId, $data);
        }

        $this->jsonResponse($record);
    }

    public function attendance(int $eventId): void
    {
        $event = $this->eventModel->find($eventId);
        if (!$event) {
            $this->jsonResponse(['error' => 'Event not found'], 404);
        }

        $records = $this->attendanceModel->getByEventId($eventId);
        $this->jsonResponse($records);
    }

    public function statistics(int $eventId): void
    {
        $event = $this->eventModel->find($eventId);
        if (!$event) {
            $this->jsonResponse(['error' => 'Event not found'], 404);
        }

        $stats = $this->attendanceModel->getStatistics($eventId);
        $this->jsonResponse($stats);
    }

    private function formatEvent(array $event): array
    {
        return [
            'id' => (int) $event['id'],
            'title' => $event['title'],
            'date' => $event['date'],
            'time' => $event['time'],
            'location' => $event['location'],
            'type' => $event['type'],
            'status' => $event['status'],
            'participants' => (int) $event['participants']
        ];
    }

    private function formatEvents(array $events): array
    {
        return array_map([$this, 'formatEvent'], $events);
    }

    private function jsonResponse(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
