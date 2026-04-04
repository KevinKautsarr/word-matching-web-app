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
}
