<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class UploadedFileCollection
{
    public const MAX_FILES = 10;
    public const MAX_FILE_KB = 10240;

    /**
     * @return array<int, UploadedFile>
     */
    public static function fromRequest(Request $request, string $arrayKey, string $singleKey): array
    {
        $files = [];

        if ($request->hasFile($arrayKey)) {
            $uploaded = $request->file($arrayKey);
            if (is_array($uploaded)) {
                foreach ($uploaded as $file) {
                    if ($file instanceof UploadedFile) {
                        $files[] = $file;
                    }
                }
            } elseif ($uploaded instanceof UploadedFile) {
                $files[] = $uploaded;
            }
        }

        if ($request->hasFile($singleKey)) {
            $file = $request->file($singleKey);
            if ($file instanceof UploadedFile) {
                $files[] = $file;
            }
        }

        return array_values($files);
    }

    public static function formatBytes(?int $bytes): string
    {
        $bytes = (int) $bytes;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        if ($bytes > 1) {
            return $bytes . ' bytes';
        }
        if ($bytes === 1) {
            return '1 byte';
        }

        return '0 bytes';
    }
}
