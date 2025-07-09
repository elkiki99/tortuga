<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Image;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'discount_price',
        'size',
        'in_stock',
        'category_id',
        'brand_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): hasMany
    {
        return $this->hasMany(Image::class)->where('is_featured', false);
    }

    public function featuredImage(): hasOne
    {
        return $this->hasOne(Image::class)->where('is_featured', true);
    }

    public function scopeSearch($query, $term)
    {
        if (trim($term) === '') return $query;

        return $query->where('name', 'like', '%' . $term . '%');
    }
}
