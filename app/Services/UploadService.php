<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\App;

final class UploadService
{
    private const ALLOWED = ['pdf', 'jpg', 'jpeg', 'png', 'csv', 'xlsx', 'xls'];

    public static function store(array $file, string $folder): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Upload file gagal.');
        }
        $max = (int) App::config('upload.max_mb', 5) * 1024 * 1024;
        if (($file['size'] ?? 0) > $max) {
            throw new \RuntimeException('Ukuran file melebihi batas ' . App::config('upload.max_mb', 5) . ' MB.');
        }
        $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED, true)) {
            throw new \RuntimeException('Format file tidak diizinkan.');
        }
        $folder = trim($folder, '/');
        $directory = BASE_PATH . '/public/uploads/' . $folder;
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException('Folder upload tidak dapat dibuat.');
        }
        $name = date('YmdHis') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $target = $directory . '/' . $name;
        if (!move_uploaded_file($file['tmp_name'], $target)) {
            throw new \RuntimeException('File tidak dapat disimpan.');
        }
        return 'uploads/' . $folder . '/' . $name;
    }
}
