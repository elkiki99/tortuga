<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

trait TrendCalculable
{
    public static function calculateCountTrend(string $table): array
    {
        $now = now();

        $current = DB::table($table)
            ->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])
            ->count();

        $previous = DB::table($table)
            ->whereBetween('created_at', [
                $now->copy()->subWeek()->startOfWeek(),
                $now->copy()->subWeek()->endOfWeek()
            ])
            ->count();

        if ($previous === 0) {
            return ['trend' => 100, 'trendUp' => true];
        }

        $change = (($current - $previous) / $previous) * 100;
        return [
            'trend' => round(abs($change), 1),
            'trendUp' => $change >= 0,
        ];
    }
}