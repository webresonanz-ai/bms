<?php

/**
 * GalleryController — /api/v1/gallery
 * GET    /api/v1/gallery          public  — list all
 * GET    /api/v1/gallery/{id}     public  — get one
 * POST   /api/v1/gallery          admin   — create
 * PUT    /api/v1/gallery/{id}     admin   — update
 * DELETE /api/v1/gallery/{id}     admin   — delete
 */

require_once __DIR__ . '/../models/GalleryModel.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../helpers/response.php';

class GalleryController
{
    private GalleryModel $model;

    public function __construct()
    {
        $this->model = new GalleryModel();
    }

    public function index(): never
    {
        respondSuccess(['items' => $this->model->all()]);
    }

    public function show(int $id): never
    {
        $item = $this->model->find($id);
        if (!$item) respondError('Gallery item not found.', 404);
        respondSuccess(['item' => $item]);
    }

    public function store(): never
    {
        requireAdmin();
        $data   = $this->input();
        $errors = $this->validate($data);
        if ($errors) respondError('Validation failed.', 422, $errors);

        $id   = $this->model->create($data);
        $item = $this->model->find($id);
        respondSuccess(['item' => $item], 201);
    }

    public function update(int $id): never
    {
        requireAdmin();
        if (!$this->model->find($id)) respondError('Gallery item not found.', 404);

        $data   = $this->input();
        $errors = $this->validate($data);
        if ($errors) respondError('Validation failed.', 422, $errors);

        $this->model->update($id, $data);
        respondSuccess(['item' => $this->model->find($id)]);
    }

    public function destroy(int $id): never
    {
        requireAdmin();
        if (!$this->model->find($id)) respondError('Gallery item not found.', 404);
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
        if (empty(trim($d['title'] ?? '')))    $e['title']    = 'Title is required.';
        if (empty(trim($d['category'] ?? ''))) $e['category'] = 'Category is required.';
        return $e;
    }
}
