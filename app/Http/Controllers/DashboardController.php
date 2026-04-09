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
            $unit->unlocked = $unit->isUnlockedFor($completedLessonIds);

            // Calculate progress per unit
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

        $hour = (int) now()->format('H');
        if ($hour >= 5 && $hour < 11) $greeting = 'Selamat pagi';
        elseif ($hour >= 11 && $hour < 15) $greeting = 'Selamat siang';
        elseif ($hour >= 15 && $hour < 18) $greeting = 'Selamat sore';
        else $greeting = 'Selamat malam';

        $randomMsg = collect([
            'Kamu luar biasa, lanjutkan!',
            'Setiap langkah kecil membawamu lebih dekat ke tujuan.',
            'Fokus dan konsistensi adalah kunci.',
            'Terus belajar, jangan menyerah!',
            'Lexora bangga dengan progresmu hari ini!'
        ])->random();

        // Daily Goal Logic
        $lastPlayed = $user->last_played_at ? $user->last_played_at->startOfDay() : null;
        $today = now()->startOfDay();
        $dailyProgress = ($lastPlayed && $lastPlayed->eq($today)) ? ($user->daily_goal_progress ?? 0) : 0;
        $goalTarget = 3; 
        $goalPercent = min(100, ($dailyProgress / $goalTarget) * 100);

        return view('dashboard.index', compact(
            'user', 'units', 'greeting', 'randomMsg', 'dailyProgress', 'goalTarget', 'goalPercent'
        ));
    }
}
