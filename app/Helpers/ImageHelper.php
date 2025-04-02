<?php

// App\Helpers\ImageHelper.php

namespace App\Helpers;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageHelper
{
    public static function convertToWebp(string $relativePath, int $quality = 85): string|false
    {
        $fullPath = storage_path('app/public/' . $relativePath);

        if (!file_exists($fullPath)) {
            return false;
        }

        $webpPath = preg_replace('/\.(jpe?g|png)$/i', '.webp', $relativePath);
        $webpFullPath = storage_path('app/public/' . $webpPath);

        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($fullPath);

            // Hier Bild auf 128x128 verkleinern (optional: aspect ratio ignorieren)
          //  $image->resize(128, 128); // fix auf genau 128x128
            $image->scaleDown(128, 128);
            // In WebP speichern
            $image->toWebp(quality: $quality)->save($webpFullPath);
        } catch (\Exception $e) {
            return false;
        }

        return \Storage::disk('public')->exists($webpPath) ? $webpPath : false;
    }

}
