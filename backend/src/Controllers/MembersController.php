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
            'role' => $member['role'],
            'section' => $member['section'],
            'joinDate' => $member['join_date'],
            'status' => $member['status'],
            'avatar' => $member['avatar'],
            'email' => $member['email'],
            'phone' => $member['phone'],
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
}