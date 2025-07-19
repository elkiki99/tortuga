<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TrendCalculable;
use App\Policies\ProductPolicy;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Image;

#[UsePolicy(ProductPolicy::class)]
class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, TrendCalculable;

    protected $fillable = [
        'code',
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

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', '%' . $term . '%')
                ->orWhereHas('category', function ($q) use ($term) {
                    $q->where('name', 'like', '%' . $term . '%');
                });
        });
    }

    public static function weeklyTrend(): array
    {
        $now = now();
        $startOfThisWeek = $now->copy()->startOfWeek();
        $startOfLastWeek = $startOfThisWeek->copy()->subWeek();
        $endOfLastWeek = $startOfThisWeek->copy()->subSecond();

        $thisWeek = self::where('created_at', '>=', $startOfThisWeek)->count();
        $lastWeek = self::whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->count();

        return self::calculateTrend($lastWeek, $thisWeek);
    }

    protected static function calculateTrend($previous, $current): array
    {
        if ($previous == 0 && $current == 0) {
            return ['trend' => '0%', 'trendUp' => false];
        }

        if ($previous == 0) {
            return ['trend' => '100%', 'trendUp' => true];
        }

        $change = (($current - $previous) / $previous) * 100;

        return [
            'trend' => number_format(abs($change), 1) . '%',
            'trendUp' => $change >= 0,
        ];
    }
}
