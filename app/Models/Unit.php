<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'icon',
        'order',
        'is_locked',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'is_locked' => 'boolean',
        'order'     => 'integer',
    ];

    // =====================
    // RELATIONSHIPS
    // =====================

    /**
     * Satu unit memiliki banyak lesson.
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    // =====================
    // SCOPES
    // =====================

    /**
     * Scope untuk mengambil unit yang tidak terkunci.
     */
    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }

    /**
     * Scope untuk mengurutkan berdasarkan kolom order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Cek apakah unit ini terbuka (tidak terkunci) untuk user.
     * Menggunakan parameter completedLessonIds agar lebih efisien.
     */
    public function isUnlockedFor(array $completedLessonIds): bool
    {
        // Prioritas 1: Jika dari database is_locked = false, PASTI TERBUKA
        if (! $this->is_locked) {
            return true;
        }

        // Prioritas 2: Jika is_locked = true, cek unit sebelumnya
        $prevUnit = self::where('order', '<', $this->order)
            ->orderBy('order', 'desc')
            ->first();

        // Jika tidak ada unit sebelumnya (unit paling pertama), maka default terbuka
        if (! $prevUnit) {
            return true;
        }

        $prevLessonIds = $prevUnit->lessons()->pluck('id')->toArray();

        // Jika unit sebelumnya tidak memiliki lesson sama sekali, langsung terbuka
        if (empty($prevLessonIds)) {
            return true;
        }

        // Cek apakah semua lesson di unit sebelumnya sudah diselesaikan
        // Yaitu jika tidak ada id lesson sebelumnya yang belum ada di array completed
        return count(array_diff($prevLessonIds, $completedLessonIds)) === 0;
    }
}
