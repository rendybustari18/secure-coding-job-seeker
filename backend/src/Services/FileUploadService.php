<?php
require_once __DIR__ . '/../../config/env.php';

class FileUploadService
{
    public static function upload(array $file, string $subfolder = ''): string|false
    {
        $dir = UPLOAD_PATH . ($subfolder ? $subfolder . '/' : '');

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $originalName = $file['name'];
        $targetPath   = $dir . $originalName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return 'uploads/' . ($subfolder ? $subfolder . '/' : '') . $originalName;
        }

        return false;
    }

    public static function delete(string $filepath): bool
    {
        $fullPath = dirname(__DIR__, 2) . '/' . $filepath;
        if (file_exists($fullPath)) {
            unlink($fullPath);
            return true;
        }

        return false;
    }

    public static function includeFile(string $filename): void
    {
        $fullPath = dirname(__DIR__, 2) . '/' . $filename;
        include $fullPath;
    }
}

