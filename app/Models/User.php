<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use App\Traits\TrendCalculable;
use Illuminate\Support\Str;
use App\Models\Wishlist;
use App\Models\Order;
use App\Models\Cart;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TrendCalculable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the user's cart.
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * Get the user's wishlist.
     */
    public function wishlist(): HasOne
    {
        return $this->hasOne(Wishlist::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the user's role.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
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
