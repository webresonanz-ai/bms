<?php

namespace BMS\Controllers;

use BMS\Models\Event;

class EventsController
{
    private Event $eventModel;

    public function __construct()
    {
        $this->eventModel = new Event();
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