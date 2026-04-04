<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Halaman utama dashboard setelah login.
     * Menampilkan unit-unit beserta status lock/unlock berdasarkan progress user.
     */
    public function index(): View
    {
        $user  = Auth::user();
        $units = Unit::with(['lessons'])->orderBy('order')->get();

        // Ambil semua lesson_id yang sudah diselesaikan oleh user ini
        $completedLessonIds = $user->progress()
            ->completed()
            ->pluck('lesson_id')
            ->toArray();

        // Hitung status lock/unlock tiap unit
        $units = $units->map(function (Unit $unit, int $index) use ($completedLessonIds) {
            return $this->resolveUnitLockStatus($unit, $index, $completedLessonIds);
        });

        return view('dashboard.index', compact('user', 'units'));
    }

    // =====================================================================
    // PRIVATE HELPERS
    // =====================================================================

    /**
     * Tentukan status unlock unit berdasarkan index dan progress user.
     *
     * Aturan:
     *   - Unit pertama (index 0) selalu terbuka
     *   - Unit berikutnya terbuka HANYA jika semua lesson di unit sebelumnya selesai
     *
     * @param  Unit   $unit
     * @param  int    $index             Urutan unit (0-based)
     * @param  array  $completedLessonIds Array lesson_id yang sudah selesai
     */
    private function resolveUnitLockStatus(Unit $unit, int $index, array $completedLessonIds): Unit
    {
        if ($index === 0) {
            $unit->is_locked = false;
            return $unit;
        }

        // Dapatkan unit sebelumnya (sudah terurut by order, jadi pakai index)
        // Kita cek dari semua lesson di unit sebelumnya apakah semua selesai
        // Karena kita map() secara berurutan, kita perlu query ulang unit sebelumnya
        // Solusi: cek apakah semua lesson milik unit ini sudah selesai semua
        // (unit ini terbuka jika prevUnit selesai — kita simpan flag unlocked dari sebelumnya)

        // Ambil lesson_id milik unit sebelumnya
        $prevUnitLessonIds = Unit::orderBy('order')
            ->skip($index - 1)
            ->first()
            ?->lessons()
            ->pluck('id')
            ->toArray() ?? [];

        $allCompleted = count($prevUnitLessonIds) > 0
            && count(array_diff($prevUnitLessonIds, $completedLessonIds)) === 0;

        $unit->is_locked = ! $allCompleted;

        return $unit;
    }
}
