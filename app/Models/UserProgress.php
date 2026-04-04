<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProgress extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_progress';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'lesson_id',
        'is_completed',
        'score',
        'time_spent',
        'attempts',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'is_completed' => 'boolean',
        'score'        => 'integer',
        'time_spent'   => 'integer',
        'attempts'     => 'integer',
    ];

    // =====================
    // RELATIONSHIPS
    // =====================

    /**
     * Progress dimiliki oleh satu user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Progress dimiliki oleh satu lesson.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    // =====================
    // SCOPES
    // =====================

    /**
     * Scope hanya progress yang sudah selesai.
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }
}
