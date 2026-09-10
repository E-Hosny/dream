<?php

namespace App\Http\Controllers\Concerns;

use App\Support\UploadedFileCollection;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

trait ValidatesUploadedDocuments
{
    protected function collectDocumentFiles(Request $request, string $arrayKey, string $singleKey): array
    {
        return UploadedFileCollection::fromRequest($request, $arrayKey, $singleKey);
    }

    protected function isAllowedDocument(UploadedFile $file): bool
    {
        $allowedMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/jpg',
            'image/png',
        ];

        $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        $mime = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());

        return in_array($mime, $allowedMimes, true) || in_array($extension, $allowedExtensions, true);
    }

    /**
     * @param  array<int, UploadedFile>  $files
     */
    protected function validateDocumentFiles(array $files, bool $required = true, int $maxFiles = UploadedFileCollection::MAX_FILES): ?string
    {
        if ($required && count($files) === 0) {
            return 'يجب رفع ملف واحد على الأقل';
        }

        if (count($files) > $maxFiles) {
            return "الحد الأقصى لعدد الملفات هو {$maxFiles}";
        }

        foreach ($files as $file) {
            if (!$this->isAllowedDocument($file)) {
                return 'نوع الملف غير مدعوم. الأنواع المسموحة: PDF, DOC, DOCX, JPG, JPEG, PNG';
            }

            if ($file->getSize() > UploadedFileCollection::MAX_FILE_KB * 1024) {
                return 'حجم أحد الملفات يتجاوز 10MB. الحد الأقصى لكل ملف: 10MB';
            }
        }

        return null;
    }
}
