<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\UserProgress;
use App\Models\UserXpLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GameController extends Controller
{
    public function play(int $lesson_id)
    {
        $lesson = Lesson::with('vocabularies')->findOrFail($lesson_id);
        $user   = Auth::user();

        if ($lesson->vocabularies->isEmpty()) {
            return redirect()->route('lessons.show', $lesson_id)
                ->with('error', 'Lesson ini belum memiliki kosakata. Coba lagi nanti.');
        }

        $vocabularies = $lesson->vocabularies->shuffle();

        return view('game.play', compact('lesson', 'vocabularies', 'user'));
    }

    public function submit(Request $request, int $lessonId)
    {
        $request->validate([
            'score'   => 'required|numeric',
            'correct' => 'required|numeric',
            'total'   => 'required|numeric',
        ]);

        $user = Auth::user();
        $lesson = Lesson::with('unit')->findOrFail($lessonId);

        $correct = (int) $request->correct;
        $total   = (int) $request->total;
        $score   = (int) $request->score;

        $xpEarned = 0;
        if ($total > 0) {
            $xpEarned = round(($correct / $total) * $lesson->xp_reward);
        }

        $isCompleted = false;
        if ($total > 0 && $correct >= ($total * 0.6)) {
            $isCompleted = true;
        }

        DB::transaction(function () use ($user, $lesson, $correct, $total, $score, $isCompleted, &$xpEarned) {
            $progress = UserProgress::firstOrNew([
                'user_id'   => $user->id,
                'lesson_id' => $lesson->id
            ]);

            $wasCompleted = $progress->exists && $progress->is_completed;

            $progress->attempts = ($progress->attempts ?? 0) + 1;
            // Pertahankan status completed jika sebelumnya sudah pernah selesai
            $progress->is_completed = $progress->is_completed || $isCompleted;
            $progress->score = max((int)$progress->score, $score);
            $progress->time_spent = 60;
            $progress->save();

            // Daily Goal & Streak Logic
            $today = now()->startOfDay();
            $lastPlayedDate = $user->last_played_at ? clone $user->last_played_at->startOfDay() : null;

            if (!$lastPlayedDate || $lastPlayedDate->ne($today)) {
                $user->daily_goal_progress = 0; // Reset daily goal progress for new day
            }
            $user->daily_goal_progress += 1;

            if ($user->daily_goal_progress === 3) {
                $xpEarned += 50;
                session()->flash('success_goal', 'Kamu menyelesaikan target harian 3 lesson! Bonus +50 XP 🎉');
            }

            // Streak check
            if ($lastPlayedDate && $lastPlayedDate->eq(now()->subDay()->startOfDay())) {
                $user->streak += 1;
                session()->flash('streak_up', true); // trigger animation in frontend
                if ($user->streak % 7 === 0) {
                    $xpEarned += 100;
                    session()->flash('success_streak', 'Streak ' . $user->streak . ' Hari! Bonus +100 XP 🔥');
                }
            } elseif (!$lastPlayedDate || $lastPlayedDate->lt(now()->subDay()->startOfDay())) {
                // Streak loss or first time
                $user->streak = 1;
            }

            $user->last_played_at = now();

            // Perfect Score Bonus
            if ($correct === $total && $total > 0) {
                $xpEarned += 15;
                session()->flash('perfect_score', 'Perfect! Bonus +15 XP 🎯');
            }

            // Hanya berikan XP basik jika sekarang completed DAN belum pernah di-completed sebelumnya
            // Namun bonus streak / daily goal / perfect score selalu diberikan
            // Wait, to prevent farming, let's only give base XP if it was not completed previously.
            // But perfect score bonus and daily goals should be given. We just update User's total XP.
            if ($isCompleted && !$wasCompleted) {
                // Base $xpEarned includes calculated (correct/total)*reward + bonuses.
            } elseif ($wasCompleted) {
                // If it was already completed, remove the base lesson reward from xpEarned, 
                // keeping only perfect score and daily goal bonuses!
                $baseReward = round(($correct / $total) * $lesson->xp_reward);
                $xpEarned -= $baseReward; 
            }

            if ($xpEarned > 0) {
                $user->xp += $xpEarned;
                $user->level = floor($user->xp / 100) + 1;

                UserXpLog::create([
                    'user_id'   => $user->id,
                    'xp_gained' => $xpEarned,
                    'activity'  => 'Penyelesaian lesson: ' . $lesson->title . ' + Bonus'
                ]);
            }
            
            $user->save();
        });

        session([
            'game_result' => [
                'score'      => $score,
                'correct'    => $correct,
                'total'      => $total,
                'time_spent' => 60,
                'xp_earned'  => $xpEarned,
                'completed'  => $isCompleted
            ]
        ]);

        $nextLesson = Lesson::where('unit_id', $lesson->unit_id)
            ->where('order', '>', $lesson->order)
            ->orderBy('order')
            ->first();

        $redirectUrl = $nextLesson 
            ? route('lessons.show', $nextLesson->id) 
            : route('units.show', $lesson->unit_id);

        return response()->json([
            'redirect' => $redirectUrl
        ]);
    }

    public function result(int $lessonId)
    {
        $user = Auth::user();
        $lesson = Lesson::with('unit')->findOrFail($lessonId);
        
        $progress = $user->progress()->where('lesson_id', $lesson->id)->first();
        
        $nextLesson = Lesson::where('unit_id', $lesson->unit_id)
            ->where('order', '>', $lesson->order)
            ->orderBy('order')
            ->first();

        $sessionData = session('game_result', []);

        $result = [
            'score'      => $sessionData['score'] ?? 0,
            'correct'    => $sessionData['correct'] ?? 0,
            'total'      => $sessionData['total'] ?? 0,
            'time_spent' => $sessionData['time_spent'] ?? 60,
            'xp_earned'  => $sessionData['xp_earned'] ?? 0,
            'completed'  => $sessionData['completed'] ?? false
        ];

        return view('game.result', compact('user', 'lesson', 'progress', 'nextLesson', 'result'));
    }
}
