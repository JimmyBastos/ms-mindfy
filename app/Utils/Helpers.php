<?php

namespace App\Utils;

class Helpers
{

    static function slugify(string $text): string
    {
        return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($text)), "-");
    }
}