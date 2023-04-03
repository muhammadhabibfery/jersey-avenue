<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait ImageHandler
{
    /**
     * set the image file
     */
    private static function setFileName(string $fileName): string
    {
        $result = explode('.', $fileName);
        $result = head($result) . rand(0, 100) . '.' . last($result);
        return $result;
    }

    /**
     * delete the image file from application directory
     */
    private static function deleteImage(string $path): void
    {
        Storage::disk('public')->delete($path);
    }
}
