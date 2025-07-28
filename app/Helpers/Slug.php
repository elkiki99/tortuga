<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class Slug
{
    public static function generate(string $name, string $model, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $i = 2;

        $query = $model::where('slug', $slug);
        if ($ignoreId) {
            $query = $query->where('id', '!=', $ignoreId);
        }

        while ($query->exists()) {
            $slug = $original . '-' . $i++;
            $query = $model::where('slug', $slug);
            if ($ignoreId) {
                $query = $query->where('id', '!=', $ignoreId);
            }
        }

        return $slug;
    }
}
