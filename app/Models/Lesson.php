<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'unit_id',
        'title',
        'description',
        'order',
        'is_locked',
        'xp_reward',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'is_locked' => 'boolean',
        'order'     => 'integer',
        'xp_reward' => 'integer',
    ];

    // =====================
    // RELATIONSHIPS
    // =====================

    /**
     * Lesson dimiliki oleh satu unit.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Satu lesson memiliki banyak kosakata.
     */
    public function vocabularies(): HasMany
    {
        return $this->hasMany(Vocabulary::class);
    }

    /**
     * Satu lesson memiliki banyak progress dari berbagai user.
     */
    public function userProgress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    // =====================
    // SCOPES
    // =====================

    /**
     * Scope untuk mengambil lesson yang tidak terkunci.
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

    // =====================
    // HELPERS
    // =====================

    /**
     * Cek apakah lesson ini sudah terbuka untuk user berdasarkan
     * array lesson_id yang sudah diselesaikan.
     *
     * Aturan:
     * - Jika is_locked = false  → selalu terbuka
     * - Lesson pertama (order=1 atau tidak ada lesson sebelumnya) → terbuka
     * - Lesson sebelumnya (order-1) sudah ada di $completedLessonIds → terbuka
     */
    public function unlockedFor(array $completedLessonIds): bool
    {
        // Tidak di-lock sama sekali
        if (!$this->is_locked) {
            return true;
        }

        // Cari lesson sebelumnya dalam unit yang sama
        $previousLesson = static::where('unit_id', $this->unit_id)
            ->where('order', $this->order - 1)
            ->first();

        // Tidak ada lesson sebelumnya → ini lesson pertama, terbuka
        if (!$previousLesson) {
            return true;
        }

        // Lesson sebelumnya sudah selesai
        return in_array($previousLesson->id, $completedLessonIds);
    }
}
