<?php

namespace BMS\Controllers;

use BMS\Models\Member;

class MembersController
{
    private Member $memberModel;

    public function __construct()
    {
        $this->memberModel = new Member();
    }

    public function index(): void
    {
        $members = $this->memberModel->all();
        $this->jsonResponse($this->formatMembers($members));
    }

    public function active(): void
    {
        $members = $this->memberModel->getActive();
        $this->jsonResponse($this->formatMembers($members));
    }

    public function bySection(): void
    {
        $members = $this->memberModel->all();
        $sections = [];

        foreach ($members as $member) {
            $section = $member['section'];
            if (!isset($sections[$section])) {
                $sections[$section] = [];
            }
            $sections[$section][] = $this->formatMember($member);
        }

        $this->jsonResponse($sections);
    }

    public function store(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $id = $this->memberModel->create($data);
        $member = $this->memberModel->find($id);

        $this->jsonResponse($this->formatMember($member), 201);
    }

    public function updateStatus(int $id): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['status'])) {
            $this->jsonResponse(['error' => 'Status required'], 400);
            return;
        }

        $this->memberModel->updateStatus($id, $data['status']);
        $this->jsonResponse(['message' => 'Status updated']);
    }

    public function update(int $id): void
    {
        $allowed = ['name', 'nickname', 'email', 'fullname', 'stage_name', 'voice', 'voice_type', 'birth_place', 'birth_date', 'domicile', 'phone', 'role', 'section', 'join_date', 'status', 'performances', 'avatar', 'year_join', 'field_of_work'];
        $data = json_decode(file_get_contents('php://input'), true);
        $filtered = array_intersect_key($data, array_flip($allowed));
        if (isset($data['joinDate']) && !isset($filtered['join_date'])) {
            $filtered['join_date'] = $data['joinDate'];
        }
        $this->memberModel->update($id, $filtered);
        $member = $this->memberModel->find($id);
        $this->jsonResponse($this->formatMember($member));
    }

    public function import(): void
    {
        if (!isset($_FILES['excel']) || $_FILES['excel']['error'] !== UPLOAD_ERR_OK) {
            $this->jsonResponse(['error' => 'Excel file is required'], 400);
            return;
        }

        $filePath = $_FILES['excel']['tmp_name'];

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        } catch (\Throwable $e) {
            $this->jsonResponse(['error' => 'Invalid Excel file: ' . $e->getMessage()], 400);
            return;
        }

        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        if (count($rows) < 2) {
            $this->jsonResponse(['error' => 'Excel file is empty'], 400);
            return;
        }

        $header = array_map(fn($h) => strtolower(trim((string)$h)), $rows[0]);
        $allowed = ['name', 'nickname', 'email', 'stage_name', 'birth_place', 'birth_date', 'domicile', 'phone', 'year_join', 'field_of_work', 'role', 'section', 'join_date', 'status', 'performances', 'avatar'];
        $header = array_intersect($header, $allowed);

        if (empty($header) || !in_array('name', $header) || !in_array('email', $header)) {
            $this->jsonResponse(['error' => 'Excel must contain at least name and email columns'], 400);
            return;
        }

        $inserted = 0;
        $skipped = 0;
        $skippedList = [];

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            if (empty($row[array_search('name', $header)]) && empty($row[array_search('email', $header)])) {
                continue;
            }

            $data = [];
            foreach ($header as $col) {
                $idx = array_search($col, $header);
                $data[$col] = $row[$idx] ?? null;
            }

            foreach (['join_date', 'birth_date', 'year_join'] as $dateField) {
                if (!empty($data[$dateField])) {
                    $data[$dateField] = $this->parseExcelDate($data[$dateField]);
                }
            }

            $data['status'] = !empty($data['status']) ? $data['status'] : 'active';
            $data['performances'] = !empty($data['performances']) ? (int)$data['performances'] : 0;
            if (empty($data['join_date'])) {
                $data['join_date'] = date('Y-m-d');
            }

            $existing = $this->memberModel->findByEmail($data['email'] ?? '');
            if ($existing) {
                $skipped++;
                $skippedList[] = [
                    'row' => $i + 1,
                    'name' => $data['name'] ?? null,
                    'email' => $data['email'] ?? null,
                    'reason' => 'Duplicate email'
                ];
                continue;
            }

            $this->memberModel->create($data);
            $inserted++;
        }

        $this->jsonResponse([
            'message' => 'Import completed',
            'inserted' => $inserted,
            'skipped' => $skipped,
            'skipped_list' => $skippedList
        ]);
    }

    public function destroy(int $id): void
    {
        $this->memberModel->delete($id);
        $this->jsonResponse(['message' => 'Member deleted']);
    }

    private function formatMember(array $member): array
    {
        return [
            'id' => (int) $member['id'],
            'name' => $member['name'],
            'nickname' => $member['nickname'] ?? null,
            'email' => $member['email'],
            'stage_name' => $member['stage_name'] ?? null,
            'birth_place' => $member['birth_place'] ?? null,
            'birth_date' => $member['birth_date'] ?? null,
            'domicile' => $member['domicile'] ?? null,
            'phone' => $member['phone'],
            'year_join' => $member['year_join'] ?? null,
            'field_of_work' => $member['field_of_work'] ?? null,
            'role' => $member['role'],
            'section' => $member['section'],
            'joinDate' => $member['join_date'],
            'status' => $member['status'],
            'avatar' => $member['avatar'],
            'performances' => (int) $member['performances']
        ];
    }

    private function formatMembers(array $members): array
    {
        return array_map([$this, 'formatMember'], $members);
    }

    private function jsonResponse(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    private function parseExcelDate(string $value): ?string
    {
        $value = trim($value);
        if (empty($value)) return null;
        try {
            $dt = new \DateTime($value);
            return $dt->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}