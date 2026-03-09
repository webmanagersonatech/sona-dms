<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class FileEncryptionService
{
    protected $encryptionKey;

    public function __construct()
    {
        $this->encryptionKey = config('app.encryption_key');
    }

    public function encryptFile($filePath, $originalName)
    {
        try {
            $content = Storage::get($filePath);
            $encryptedContent = Crypt::encrypt($content);
            
            $encryptedPath = $this->generateEncryptedPath($originalName);
            Storage::put($encryptedPath, $encryptedContent);
            
            // Remove original file
            Storage::delete($filePath);
            
            return [
                'success' => true,
                'encrypted_path' => $encryptedPath,
                'encryption_key' => $this->encryptionKey,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function decryptFile($encryptedPath)
    {
        try {
            $encryptedContent = Storage::get($encryptedPath);
            $decryptedContent = Crypt::decrypt($encryptedContent);
            
            return [
                'success' => true,
                'content' => $decryptedContent,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function generatePreview($encryptedPath, $mimeType)
    {
        $result = $this->decryptFile($encryptedPath);
        
        if (!$result['success']) {
            return null;
        }

        $content = $result['content'];
        
        // For images, create a temporary preview
        if (str_starts_with($mimeType, 'image/')) {
            $tempPath = tempnam(sys_get_temp_dir(), 'preview_');
            file_put_contents($tempPath, $content);
            return $tempPath;
        }

        // For PDFs, we'll use iframe in the view
        if ($mimeType === 'application/pdf') {
            $tempPath = tempnam(sys_get_temp_dir(), 'preview_') . '.pdf';
            file_put_contents($tempPath, $content);
            return $tempPath;
        }

        // For documents, return as base64 for embedding
        if (in_array($mimeType, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])) {
            return base64_encode($content);
        }

        return null;
    }

    private function generateEncryptedPath($originalName)
    {
        $timestamp = now()->format('Ymd_His');
        $random = bin2hex(random_bytes(8));
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        
        return "encrypted/files/{$timestamp}_{$random}.enc";
    }
}