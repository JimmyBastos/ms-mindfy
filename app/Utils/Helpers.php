<?php

namespace App\Utils;

use Str;

class Helpers
{

    static function slugify(string $text): string
    {
        return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($text)), "-");
    }

    public static function getPublicUrl(string $path): string
    {
        $endpoint = config('filesystems.disks.s3.endpoint');
        $bucket   = config('filesystems.disks.s3.bucket');

        $baseUrl = Str::replace('/s3/', '/object/public/', $endpoint);

        $baseUrl = Str::replaceEnd("/{$bucket}", '', $baseUrl);

        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}