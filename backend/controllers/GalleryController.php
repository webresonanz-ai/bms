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
require_once __DIR__ . '/../helpers/upload.php';

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
        $existing = $this->model->find($id);
        if (!$existing) respondError('Gallery item not found.', 404);

        $data   = $this->input();
        $errors = $this->validate($data);
        if ($errors) respondError('Validation failed.', 422, $errors);

        $this->model->update($id, $data);
        $updated = $this->model->find($id);

        // Photo replaced or removed → delete the orphaned file from storage
        if (($existing['image_url'] ?? null) !== ($updated['image_url'] ?? null)) {
            deleteGalleryFile($existing['image_url'] ?? null);
        }

        respondSuccess(['item' => $updated]);
    }

    public function destroy(int $id): never
    {
        requireAdmin();
        $item = $this->model->find($id);
        if (!$item) respondError('Gallery item not found.', 404);
        $this->model->delete($id);
        // Remove the compressed file from storage (remote URLs are ignored)
        deleteGalleryFile($item['image_url'] ?? null);
        respondSuccess(null, 200);
    }

    // ------------------------------------------------------------------
    // POST /api/v1/gallery/upload   (admin, multipart/form-data: image=<file>)
    // Compresses the image (max 1600px, WebP/JPEG) and stores it under
    // backend/uploads/gallery/. Returns the relative path for image_url.
    // ------------------------------------------------------------------
    public function upload(): never
    {
        requireAdmin();

        if (!isset($_FILES['image'])) {
            respondError('Validation failed.', 422, [
                'image' => 'An image file is required.',
            ]);
        }

        try {
            $saved = saveCompressedGalleryImage($_FILES['image']);
        } catch (RuntimeException $e) {
            respondError($e->getMessage(), 422, ['image' => $e->getMessage()]);
        }

        respondSuccess(['file' => $saved], 201);
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
        $date = trim((string) ($d['photo_date'] ?? ''));
        if ($date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $e['photo_date'] = 'Date must be in YYYY-MM-DD format.';
        }
        return $e;
    }
}
