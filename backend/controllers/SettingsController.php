<?php

/**
 * SettingsController — /api/v1/settings
 * GET    /api/v1/settings          public  — read all site settings
 * PUT    /api/v1/settings          admin   — update site settings
 * POST   /api/v1/settings/upload   admin   — upload a compressed setting image (hero bg)
 */

require_once __DIR__ . '/../models/SettingsModel.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/upload.php';

class SettingsController
{
    private SettingsModel $model;

    public function __construct()
    {
        $this->model = new SettingsModel();
    }

    public function index(): never
    {
        respondSuccess(['settings' => $this->model->all()]);
    }

    public function update(): never
    {
        requireAdmin();
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) respondError('Invalid JSON body.', 400);

        $settings = $data['settings'] ?? $data;
        if (!is_array($settings)) respondError('Invalid JSON body.', 400);

        $before = $this->model->all();
        $after  = $this->model->setMany($settings);

        // Hero background replaced or removed → delete the orphaned file
        if (($before['hero_background'] ?? null) !== ($after['hero_background'] ?? null)) {
            deleteUploadFile($before['hero_background'] ?? null);
        }

        respondSuccess(['settings' => $after]);
    }

    // ------------------------------------------------------------------
    // POST /api/v1/settings/upload   (admin, multipart/form-data: image=<file>)
    // Stores the image AS-IS (original quality, no compression) under
    // uploads/settings/. Compression applies to gallery photos only.
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
            $saved = saveOriginalImage($_FILES['image'], 'settings');
        } catch (RuntimeException $e) {
            respondError($e->getMessage(), 422, ['image' => $e->getMessage()]);
        }

        respondSuccess(['file' => $saved], 201);
    }
}
