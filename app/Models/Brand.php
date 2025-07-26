<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    /** @use HasFactory<\Database\Factories\BrandFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        // 'slug',
        // 'logo_path',
        'description',
    ];

    public function scopeSearch($query, $term)
    {
        if (trim($term) === '') return $query;

        return $query->where('name', 'like', '%' . $term . '%');
    }
}
