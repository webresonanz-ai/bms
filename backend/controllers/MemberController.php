<?php

/**
 * MemberController — /api/v1/members
 * GET    /api/v1/members          public  — list all
 * GET    /api/v1/members/{id}     public  — get one
 * POST   /api/v1/members          admin   — create
 * PUT    /api/v1/members/{id}     admin   — update
 * DELETE /api/v1/members/{id}     admin   — delete
 */

require_once __DIR__ . '/../models/MemberModel.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../helpers/response.php';

class MemberController
{
    private MemberModel $model;

    public function __construct()
    {
        $this->model = new MemberModel();
    }

    public function index(): never
    {
        respondSuccess(['members' => $this->model->all()]);
    }

    public function show(int $id): never
    {
        $member = $this->model->find($id);
        if (!$member) respondError('Member not found.', 404);
        respondSuccess(['member' => $member]);
    }

    public function store(): never
    {
        requireAdmin();
        $data   = $this->input();
        $errors = $this->validate($data);
        if ($errors) respondError('Validation failed.', 422, $errors);

        $id     = $this->model->create($data);
        $member = $this->model->find($id);
        respondSuccess(['member' => $member], 201);
    }

    public function update(int $id): never
    {
        requireAdmin();
        if (!$this->model->find($id)) respondError('Member not found.', 404);

        $data   = $this->input();
        $errors = $this->validate($data);
        if ($errors) respondError('Validation failed.', 422, $errors);

        $this->model->update($id, $data);
        respondSuccess(['member' => $this->model->find($id)]);
    }

    public function destroy(int $id): never
    {
        requireAdmin();
        if (!$this->model->find($id)) respondError('Member not found.', 404);
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
        if (empty(trim($d['name'] ?? '')))     $e['name']     = 'Name is required.';
        if (empty(trim($d['role'] ?? '')))     $e['role']     = 'Role is required.';
        $roles = ['Sopran','Alto','Tenor','Bass'];
        if (!in_array($d['role'] ?? '', $roles, true))
            $e['role'] = 'Role must be one of: ' . implode(', ', $roles) . '.';
        if (empty(trim($d['section'] ?? '')))  $e['section']  = 'Section is required.';
        $sections = ['Soprano','Alto','Tenor','Bass'];
        if (!in_array($d['section'] ?? '', $sections, true))
            $e['section'] = 'Section must be one of: ' . implode(', ', $sections) . '.';
        if (empty(trim($d['year_join'] ?? ''))) $e['year_join'] = 'Year join is required.';
        $year = (int) ($d['year_join'] ?? 0);
        if ($year < 1990 || $year > (int) date('Y'))
            $e['year_join'] = 'Joined year is invalid.';
        if (empty(trim($d['email'] ?? '')))    $e['email']    = 'Email is required.';
        return $e;
    }
}
