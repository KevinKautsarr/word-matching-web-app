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
}
