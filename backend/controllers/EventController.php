<?php

/**
 * EventController — /api/v1/events
 * GET    /api/v1/events          public  — list all
 * GET    /api/v1/events/{id}     public  — get one
 * POST   /api/v1/events          admin   — create
 * PUT    /api/v1/events/{id}     admin   — update
 * DELETE /api/v1/events/{id}     admin   — delete
 */

require_once __DIR__ . '/../models/EventModel.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../helpers/response.php';

class EventController
{
    private EventModel $model;

    public function __construct()
    {
        $this->model = new EventModel();
    }

    public function index(): never
    {
        respondSuccess(['events' => $this->model->all()]);
    }

    public function show(int $id): never
    {
        $event = $this->model->find($id);
        if (!$event) respondError('Event not found.', 404);
        respondSuccess(['event' => $event]);
    }

    public function store(): never
    {
        requireAdmin();
        $data   = $this->input();
        $errors = $this->validate($data);
        if ($errors) respondError('Validation failed.', 422, $errors);

        $id    = $this->model->create($data);
        $event = $this->model->find($id);
        respondSuccess(['event' => $event], 201);
    }

    public function update(int $id): never
    {
        requireAdmin();
        if (!$this->model->find($id)) respondError('Event not found.', 404);

        $data   = $this->input();
        $errors = $this->validate($data);
        if ($errors) respondError('Validation failed.', 422, $errors);

        $this->model->update($id, $data);
        respondSuccess(['event' => $this->model->find($id)]);
    }

    public function destroy(int $id): never
    {
        requireAdmin();
        if (!$this->model->find($id)) respondError('Event not found.', 404);
        $this->model->delete($id);
        respondSuccess(null, 200);
    }

    private function input(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) respondError('Invalid JSON body.', 400);
        return $data;
    }

    private function validate(array $d): array
    {
        $e = [];
        if (empty(trim($d['title'] ?? '')))       $e['title']       = 'Title is required.';
        if (empty($d['date'] ?? ''))               $e['date']        = 'Date is required.';
        if (empty($d['time'] ?? ''))               $e['time']        = 'Time is required.';
        if (empty(trim($d['venue'] ?? '')))        $e['venue']       = 'Venue is required.';
        if (empty(trim($d['city'] ?? '')))         $e['city']        = 'City is required.';
        if (empty(trim($d['description'] ?? '')))  $e['description'] = 'Description is required.';
        if (!in_array($d['status'] ?? '', ['upcoming', 'past'], true))
            $e['status'] = 'Status must be upcoming or past.';
        return $e;
    }
}
