<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Stats extends Model
{
    /** @use HasFactory<\Database\Factories\StatsFactory> */
    use HasFactory;

    protected $casts = [
        'date' => 'date',
    ];

    protected $fillable = [
        'orders_count',
        'total_revenue',
    ];

    public static function totalRevenue()
    {
        return self::sum('total_revenue');
    }

    public static function totalOrders()
    {
        return self::sum('orders_count');
    }

    public static function revenueTrend($type = 'week'): array
    {
        $today = now();
        $currentPeriod = self::query()
            ->whereBetween('date', self::periodRange($type, $today))
            ->sum('total_revenue');

        $previousPeriod = self::query()
            ->whereBetween('date', self::periodRange($type, $today->copy()->sub($type === 'week' ? '1 week' : '1 month')))
            ->sum('total_revenue');

        return self::calculateTrend($currentPeriod, $previousPeriod);
    }

    public static function ordersTrend($type = 'week'): array
    {
        $today = now();
        $currentPeriod = self::query()
            ->whereBetween('date', self::periodRange($type, $today))
            ->sum('orders_count');

        $previousPeriod = self::query()
            ->whereBetween('date', self::periodRange($type, $today->copy()->sub($type === 'week' ? '1 week' : '1 month')))
            ->sum('orders_count');

        return self::calculateTrend($currentPeriod, $previousPeriod);
    }

    protected static function periodRange($type, Carbon $date): array
    {
        return match ($type) {
            'week' => [
                $date->copy()->startOfWeek(),
                $date->copy()->endOfWeek(),
            ],
            'month' => [
                $date->copy()->startOfMonth(),
                $date->copy()->endOfMonth(),
            ],
        };
    }

    protected static function calculateTrend($current, $previous)
    {
        if ($previous == 0) {
            return [
                'trend' => $current > 0 ? 100 : 0,
                'trendUp' => $current > 0,
            ];
        }

        $trend = (($current - $previous) / $previous) * 100;

        return [
            'trend' => round($trend, 2),
            'trendUp' => $trend >= 0,
        ];
    }
}
