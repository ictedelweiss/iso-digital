<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class SignatureService
{
    /**
     * Process and store a base64 signature
     */
    public function storeSignature(string $base64Data, string $prefix = 'signature'): ?string
    {
        if (!str_starts_with($base64Data, 'data:image')) {
            return null;
        }

        // Extract base64 data
        $base64Data = preg_replace('/^data:image\/\w+;base64,/', '', $base64Data);
        $imageData = base64_decode($base64Data);

        if (!$imageData) {
            return null;
        }

        // Generate unique filename
        $filename = 'signatures/' . $prefix . '_' . date('Ymd_His') . '_' . uniqid() . '.png';

        // Store the signature
        Storage::disk('public')->put($filename, $imageData);

        return $filename;
    }

    /**
     * Compress an existing signature file
     */
    public function compressSignature(string $path, int $quality = 80): bool
    {
        $fullPath = Storage::disk('public')->path($path);

        if (!file_exists($fullPath)) {
            return false;
        }

        try {
            // Read the image
            $imageData = file_get_contents($fullPath);
            $image = imagecreatefromstring($imageData);

            if (!$image) {
                return false;
            }

            // Get dimensions
            $width = imagesx($image);
            $height = imagesy($image);

            // If image is too large, resize it
            $maxWidth = 400;
            if ($width > $maxWidth) {
                $ratio = $maxWidth / $width;
                $newWidth = (int) ($width * $ratio);
                $newHeight = (int) ($height * $ratio);

                $resized = imagecreatetruecolor($newWidth, $newHeight);

                // Preserve transparency
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
                imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);

                imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $resized;
            }

            // Save as PNG with compression
            imagepng($image, $fullPath, 9);
            imagedestroy($image);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get signature storage info
     */
    public function getStorageInfo(): array
    {
        $path = 'signatures';
        $files = Storage::disk('public')->files($path);

        $totalSize = 0;
        $fileCount = count($files);

        foreach ($files as $file) {
            $totalSize += Storage::disk('public')->size($file);
        }

        return [
            'file_count' => $fileCount,
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
        ];
    }

    /**
     * Delete a signature file
     */
    public function deleteSignature(string $path): bool
    {
        if (!$path) {
            return false;
        }

        return Storage::disk('public')->delete($path);
    }

    /**
     * Format bytes to human readable
     */
    protected function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }
}
