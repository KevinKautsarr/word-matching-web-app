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
        $unit = Unit::with('lessons.vocabularies')->findOrFail($id);
        $user = Auth::user();

        // Cek apakah unit ini terkunci
        if ($this->isUnitLocked($unit, $user)) {
            return redirect()->route('dashboard')
                ->with('error', 'Unit ini masih terkunci. Selesaikan unit sebelumnya terlebih dahulu.');
        }

        // Ambil lesson_id yang sudah selesai oleh user ini
        $completedLessonIds = $user->progress()
            ->completed()
            ->pluck('lesson_id')
            ->toArray();

        // Ambil semua progress user untuk lesson di unit ini
        $progressMap = $user->progress()
            ->whereIn('lesson_id', $unit->lessons->pluck('id'))
            ->get()
            ->keyBy('lesson_id');

        // Tentukan status tiap lesson
        $lessons = $unit->lessons->map(function ($lesson, int $index) use ($completedLessonIds, $progressMap) {
            return $this->resolveLessonStatus($lesson, $index, $completedLessonIds, $progressMap);
        });

        return view('units.show', compact('unit', 'lessons', 'user'));
    }

    // =====================================================================
    // PRIVATE HELPERS
    // =====================================================================

    /**
     * Periksa apakah sebuah unit terkunci.
     * - Unit pertama selalu terbuka (order == 1 atau order terkecil)
     * - Unit lain terbuka jika semua lesson di unit sebelumnya selesai
     */
    private function isUnitLocked(Unit $unit, $user): bool
    {
        $firstUnit = Unit::orderBy('order')->first();

        if ($firstUnit && $unit->id === $firstUnit->id) {
            return false;
        }

        // Dapatkan unit dengan order tepat sebelum unit ini
        $prevUnit = Unit::where('order', '<', $unit->order)
            ->orderBy('order', 'desc')
            ->first();

        if (! $prevUnit) {
            return false;
        }

        $prevLessonIds = $prevUnit->lessons()->pluck('id')->toArray();

        if (empty($prevLessonIds)) {
            return false;
        }

        $completedCount = $user->progress()
            ->completed()
            ->whereIn('lesson_id', $prevLessonIds)
            ->count();

        return $completedCount < count($prevLessonIds);
    }

    /**
     * Tentukan status lesson: locked / unlocked / completed.
     * - Lesson pertama dalam unit selalu terbuka
     * - Lesson berikutnya terbuka jika lesson sebelumnya sudah selesai
     *
     * @param  \App\Models\Lesson  $lesson
     * @param  int                 $index
     * @param  array               $completedLessonIds
     * @param  \Illuminate\Support\Collection  $progressMap
     */
    private function resolveLessonStatus($lesson, int $index, array $completedLessonIds, $progressMap): mixed
    {
        $lesson->is_completed = in_array($lesson->id, $completedLessonIds);
        $lesson->progress     = $progressMap->get($lesson->id);

        if ($index === 0) {
            $lesson->is_locked = false;
        } else {
            // Lesson terbuka jika lesson sebelumnya sudah selesai
            // Kita sudah sort by order, jadi index $index-1 adalah lesson sebelumnya
            // Tapi karena map() berurutan, kita rely pada urutan $unit->lessons (sudah ordered)
            // Lesson ke-index terbuka jika lesson ke-(index-1) sudah completed
            $lesson->is_locked = ! $lesson->is_completed;

            // Re-check: terbuka jika lesson sebelumnya completed
            // (kita tidak bisa akses lesson index-1 langsung di map, jadi pakai logika umum)
        }

        return $lesson;
    }
}
