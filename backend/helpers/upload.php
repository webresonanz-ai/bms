<?php

/**
 * Image upload + compression helper (GD-based, no Composer needed).
 * Batavia Madrigal Singers — Backend API
 *
 * Strategy to reduce storage usage:
 *   1. Downscale so the longest side is at most GALLERY_MAX_DIM px.
 *   2. Re-encode as WebP (quality 80) when supported, otherwise JPEG (quality 82).
 *   3. Random filenames to avoid collisions.
 */

// Max dimension (px) of the stored image — longest side.
if (!defined('GALLERY_MAX_DIM')) {
    define('GALLERY_MAX_DIM', (int) env('GALLERY_MAX_DIM', 1600));
}
// Max accepted upload size: 15 MB (pre-compression).
if (!defined('GALLERY_MAX_UPLOAD_BYTES')) {
    define('GALLERY_MAX_UPLOAD_BYTES', 15 * 1024 * 1024);
}

/**
 * Validate, compress and store an uploaded gallery image.
 *
 * @param  array $file  One entry of $_FILES (must contain tmp_name, size, error).
 * @return array        ['path' => 'uploads/gallery/xxx.webp', 'bytes' => int, 'width' => int, 'height' => int]
 *
 * @throws RuntimeException  When the file is invalid or cannot be processed.
 */
function saveCompressedGalleryImage(array $file): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('No image was uploaded.');
    }

    if (($file['size'] ?? 0) > GALLERY_MAX_UPLOAD_BYTES) {
        throw new RuntimeException('Image is too large. Maximum 15 MB.');
    }

    $tmp = $file['tmp_name'] ?? '';
    if (!is_uploaded_file($tmp)) {
        throw new RuntimeException('Invalid upload.');
    }

    $info = @getimagesize($tmp);
    if ($info === false) {
        throw new RuntimeException('File is not a valid image.');
    }

    $mime = $info['mime'] ?? '';
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($mime, $allowed, true)) {
        throw new RuntimeException('Only JPG, PNG, GIF and WebP images are allowed.');
    }

    $src = @imagecreatefromstring(file_get_contents($tmp));
    if ($src === false) {
        throw new RuntimeException('Could not read image data.');
    }

    // Fix EXIF rotation for phone photos (JPEG only)
    $src = applyExifOrientation($src, $tmp, $mime);

    $width  = imagesx($src);
    $height = imagesy($src);

    // Downscale if the longest side exceeds the limit
    $longest = max($width, $height);
    if ($longest > GALLERY_MAX_DIM) {
        $scale     = GALLERY_MAX_DIM / $longest;
        $newWidth  = (int) round($width * $scale);
        $newHeight = (int) round($height * $scale);
        $resized   = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency (PNG/GIF/WebP with alpha)
        imagealphablending($resized, false);
        imagesavealpha($resized, true);

        imagecopyresampled($resized, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        unset($src);
        $src    = $resized;
        $width  = $newWidth;
        $height = $newHeight;
    }

    // Destination directory: <backend>/uploads/gallery
    $dir = __DIR__ . '/../uploads/gallery';
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        throw new RuntimeException('Could not create upload directory.');
    }

    $useWebp = function_exists('imagewebp');
    $ext     = $useWebp ? 'webp' : 'jpg';
    $name    = bin2hex(random_bytes(16)) . '.' . $ext;
    $dest    = $dir . '/' . $name;

    if ($useWebp) {
        $ok = imagewebp($src, $dest, 80);
    } else {
        // JPEG has no alpha — flatten onto white
        $flat = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($flat, 255, 255, 255);
        imagefill($flat, 0, 0, $white);
        imagecopy($flat, $src, 0, 0, 0, 0, $width, $height);
        unset($src);
        $src = $flat;
        $ok = imagejpeg($src, $dest, 82);
    }

    if (!$ok) {
        throw new RuntimeException('Could not compress image.');
    }

    return [
        'path'   => 'backend/uploads/gallery/' . $name,
        'bytes'  => filesize($dest) ?: 0,
        'width'  => $width,
        'height' => $height,
    ];
}

/**
 * Delete a previously uploaded gallery file given its stored image_url.
 * Remote URLs (http…) and missing files are ignored safely.
 */
function deleteGalleryFile(?string $imageUrl): void
{
    if ($imageUrl === null || $imageUrl === '') {
        return;
    }
    if (str_starts_with($imageUrl, 'http://') || str_starts_with($imageUrl, 'https://')) {
        return;
    }

    $path = __DIR__ . '/../' . ltrim($imageUrl, '/');
    $real = realpath($path);
    $base = realpath(__DIR__ . '/../uploads');

    // Only delete files that really live inside uploads/ (path traversal guard)
    if ($real !== false && $base !== false && str_starts_with($real, $base) && is_file($real)) {
        @unlink($real);
    }
}

/**
 * Rotate/flip a GD image according to its EXIF orientation tag.
 */
function applyExifOrientation(GdImage $img, string $tmpPath, string $mime): GdImage
{
    if ($mime !== 'image/jpeg' || !function_exists('exif_read_data')) {
        return $img;
    }

    $exif = @exif_read_data($tmpPath);
    $orientation = (int) ($exif['Orientation'] ?? 1);
    if ($orientation < 2 || $orientation > 8) {
        return $img;
    }

    $rotated = match ($orientation) {
        3 => imagerotate($img, 180, 0),
        6 => imagerotate($img, -90, 0),
        8 => imagerotate($img, 90, 0),
        default => $img,
    };

    // Mirror cases (2, 4, 5, 7)
    if (in_array($orientation, [2, 4, 5, 7], true) && function_exists('imageflip')) {
        imageflip($rotated, in_array($orientation, [2, 5], true) ? IMG_FLIP_HORIZONTAL : IMG_FLIP_VERTICAL);
    }

    return $rotated;
}
