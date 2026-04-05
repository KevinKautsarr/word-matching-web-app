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

        // Hitung status lock/unlock dan progress tiap unit
        $units = $units->map(function (Unit $unit) use ($completedLessonIds) {
            // Set status unlocked menggunakan method di model
            $unit->unlocked = $unit->isUnlockedFor($completedLessonIds);

            // Hitung progress
            $totalLessons = $unit->lessons->count();
            if ($totalLessons > 0) {
                $unitLessonIds = $unit->lessons->pluck('id')->toArray();
                $completedInUnit = count(array_intersect($unitLessonIds, $completedLessonIds));
                $unit->progress = round(($completedInUnit / $totalLessons) * 100);
            } else {
                $unit->progress = 0;
            }

            return $unit;
        });

        return view('dashboard.index', compact('user', 'units'));
    }
}
