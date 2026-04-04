<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LessonController extends Controller
{
    /**
     * Tampilkan detail lesson beserta vocabulary yang dimilikinya.
     * Cek apakah lesson boleh diakses (locking system).
     */
    public function show(int $id): View|RedirectResponse
    {
        $lesson = Lesson::with(['unit', 'vocabularies'])->findOrFail($id);
        $user   = Auth::user();

        // Cek akses lesson
        if ($this->isLessonLocked($lesson, $user)) {
            return redirect()->route('units.show', $lesson->unit_id)
                ->with('error', 'Lesson ini masih terkunci. Selesaikan lesson sebelumnya terlebih dahulu.');
        }

        // Ambil progress user untuk lesson ini (jika ada)
        $progress = $user->progress()
            ->where('lesson_id', $lesson->id)
            ->first();

        return view('lessons.show', compact('lesson', 'user', 'progress'));
    }

    // =====================================================================
    // PRIVATE HELPERS
    // =====================================================================

    /**
     * Cek apakah lesson terkunci untuk user yang sedang login.
     *
     * Aturan:
     *   - Lesson pertama dalam unit selalu terbuka
     *   - Lesson berikutnya terbuka jika lesson sebelumnya (berdasarkan order) sudah selesai
     */
    private function isLessonLocked(Lesson $lesson, $user): bool
    {
        // Temukan lesson pertama dalam unit yang sama
        $firstLesson = Lesson::where('unit_id', $lesson->unit_id)
            ->orderBy('order')
            ->first();

        if ($firstLesson && $lesson->id === $firstLesson->id) {
            return false;
        }

        // Temukan lesson dengan order tepat sebelum lesson ini
        $prevLesson = Lesson::where('unit_id', $lesson->unit_id)
            ->where('order', '<', $lesson->order)
            ->orderBy('order', 'desc')
            ->first();

        if (! $prevLesson) {
            return false;
        }

        // Cek apakah lesson sebelumnya sudah selesai
        $prevCompleted = $user->progress()
            ->completed()
            ->where('lesson_id', $prevLesson->id)
            ->exists();

        return ! $prevCompleted;
    }
}
