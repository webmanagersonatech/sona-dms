<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidFileType implements Rule
{
    protected $allowedTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/jpeg',
        'image/png',
        'image/gif',
        'application/zip',
        'application/x-zip-compressed',
    ];

    public function passes($attribute, $value)
    {
        if (!$value instanceof \Illuminate\Http\UploadedFile) {
            return false;
        }

        return in_array($value->getMimeType(), $this->allowedTypes);
    }

    public function message()
    {
        return 'The :attribute must be a valid file type (PDF, DOC, DOCX, JPG, PNG, GIF, ZIP).';
    }
}