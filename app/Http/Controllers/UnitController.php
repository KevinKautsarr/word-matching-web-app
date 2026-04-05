<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UnitController extends Controller
{
    /**
     * Tampilkan detail unit beserta daftar lessons dan status masing-masing.
     */
    public function show(int $id): View|RedirectResponse
    {
        // 5. Pastikan urutan lesson berdasarkan 'order'
        $unit = Unit::with([
            'lessons' => function ($query) {
                $query->orderBy('order');
            }, 
            'lessons.vocabularies'
        ])->findOrFail($id);

        // 1. Ambil user
        $user = Auth::user();

        // 2. Ambil completed lesson
        $completedLessonIds = $user->progress()
            ->completed()
            ->pluck('lesson_id')
            ->toArray();

        // Cek apakah unit ini terkunci 
        // Menggunakan method isUnlockedFor yang baru kita buat
        if (! $unit->isUnlockedFor($completedLessonIds)) {
            return redirect()->route('dashboard')
                ->with('error', 'Unit ini masih terkunci. Selesaikan unit sebelumnya terlebih dahulu.');
        }

        // 3 & 4. Loop semua lesson dan set RULE UNLOCK
        $lessons = $unit->lessons->values()->map(function ($lesson, $index) use ($completedLessonIds, $unit) {
            $lesson->completed = in_array($lesson->id, $completedLessonIds);

            if ($index === 0) {
                $lesson->unlocked = true;
            } else {
                $prevLesson = $unit->lessons[$index - 1];
                $lesson->unlocked = in_array($prevLesson->id, $completedLessonIds);
            }

            return $lesson;
        });

        return view('units.show', compact('unit', 'lessons', 'user'));
    }
}
