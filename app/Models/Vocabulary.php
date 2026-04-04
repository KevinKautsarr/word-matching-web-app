<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vocabulary extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'lesson_id',
        'word',
        'meaning',
        'example_sentence',
        'image_url',
    ];

    // =====================
    // RELATIONSHIPS
    // =====================

    /**
     * Kosakata dimiliki oleh satu lesson.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
