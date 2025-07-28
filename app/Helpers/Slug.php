<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class Slug
{
    public static function generate(string $name, string $model): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $i = 2;

        while ($model::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }

        return $slug;
    }
}