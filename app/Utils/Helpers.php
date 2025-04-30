<?php

namespace App\Utils;

use Storage;

class Helpers
{

    static function slugify(string $text): string
    {
        return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($text)), "-");
    }

    public static function getPublicStorageUrl(string $path): string
    {
        return preg_replace('/s3/', '/object/public/', Storage::url($path), 1);
    }
}
