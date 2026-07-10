<?php

if (!function_exists('getFileIconClass')) {
    /**
     * Получить класс иконки для типа файла
     */
    function getFileIconClass($fileType)
    {
        if (!$fileType) {
            return 'feather-file';
        }

        $icons = [
            'pdf' => 'feather-file-text',
            'word' => 'feather-file-text',
            'document' => 'feather-file-text',
            'excel' => 'feather-file',
            'spreadsheet' => 'feather-file',
            'image' => 'feather-image',
            'video' => 'feather-video',
            'audio' => 'feather-music',
            'zip' => 'feather-archive',
            'rar' => 'feather-archive',
            'archive' => 'feather-archive',
        ];

        foreach ($icons as $type => $icon) {
            if (stripos($fileType, $type) !== false) {
                return $icon;
            }
        }

        return 'feather-file';
    }
}

if (!function_exists('formatFileSize')) {
    /**
     * Форматирование размера файла
     */
    function formatFileSize($bytes)
    {
        if (!$bytes) {
            return '';
        }

        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        if ($bytes === 0) {
            return '0 Byte';
        }

        $i = floor(log($bytes) / log(1024));
        return round($bytes / pow(1024, $i), 2) . ' ' . $sizes[$i];
    }
}
