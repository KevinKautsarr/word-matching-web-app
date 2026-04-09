<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\UserProgress;
use App\Models\UserXpLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class GameController extends Controller
{
    public function play(int $lesson_id)
    {
        $lesson = Lesson::with(['vocabularies', 'unit'])->findOrFail($lesson_id);
        $user   = Auth::user();

        // ── SECURITY CHECK: Apakah lesson ini sudah terbuka? ────────────────
        $completedLessonIds = $user->progress()->completed()->pluck('lesson_id')->toArray();
        if (!$lesson->unlockedFor($completedLessonIds)) {
            return redirect()->route('dashboard')
                ->with('error', 'Ops! Kamu belum membuka lesson ini. Selesaikan lesson sebelumnya dulu ya.');
        }

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

        // ── SERVER-SIDE SECURITY CHECK: Validasi Lock/Unlock ────────────────
        $completedLessonIds = $user->progress()->completed()->pluck('lesson_id')->toArray();
        if (!$lesson->unlockedFor($completedLessonIds)) {
            return response()->json(['error' => 'Aksi terlarang. Lesson terkunci.'], 403);
        }

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
            $progress->is_completed = $progress->is_completed || $isCompleted;
            $progress->score = max((int)$progress->score, $score);
            $progress->time_spent = 60;
            $progress->save();

            // Streak & Daily Goal logic (Only if completed)
            if ($isCompleted) {
                $now = Carbon::now();
                $today = $now->copy()->startOfDay();
                $lastPlayedDate = $user->last_played_at 
                    ? Carbon::parse($user->last_played_at)->startOfDay() 
                    : null;

                if (!$lastPlayedDate || $lastPlayedDate->lt($today)) {
                    $user->daily_goal_progress = 0; 
                }
                $user->daily_goal_progress += 1;

                if ($user->daily_goal_progress === 3) {
                    $xpEarned += 50;
                    session()->flash('game_success', 'Target Harian Selesai! +50 XP 🎯');
                }

                if (!$lastPlayedDate) {
                    $user->streak = 1;
                } else {
                    if ($lastPlayedDate->eq($today)) {
                        // Already won today
                    } elseif ($lastPlayedDate->eq($today->copy()->subDay())) {
                        $user->streak += 1;
                        session()->flash('streak_up', true);
                        
                        if ($user->streak % 7 === 0) {
                            $xpEarned += 100;
                            session()->flash('game_success', 'Streak ' . $user->streak . ' Hari! Bonus +100 XP 🔥');
                        }
                    } else {
                        $user->streak = 1;
                    }
                }
                $user->last_played_at = $now;
            }

            if ($correct === $total && $total > 0) {
                $xpEarned += 15;
                session()->flash('game_success', 'Perfect! Bonus +15 XP 🎯');
            }

            if ($wasCompleted) {
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
                'score'         => $score,
                'correct'       => $correct,
                'total'         => $total,
                'time_spent'    => 60,
                'xp_earned'     => $xpEarned,
                'completed'     => $isCompleted,
                'daily_current' => $user->daily_goal_progress,
                'daily_target'  => 3, // Target tetap harian
            ]
        ]);

        $nextLesson = Lesson::where('unit_id', $lesson->unit_id)
            ->where('order', '>', $lesson->order)
            ->orderBy('order')
            ->first();

        return response()->json([
            'redirect' => route('game.result', $lessonId)
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
            'score'         => $sessionData['score']         ?? 0,
            'correct'       => $sessionData['correct']       ?? 0,
            'total'         => $sessionData['total']         ?? 0,
            'time_spent'    => $sessionData['time_spent']    ?? 60,
            'xp_earned'     => $sessionData['xp_earned']     ?? 0,
            'completed'     => $sessionData['completed']     ?? false,
            'daily_current' => $sessionData['daily_current'] ?? 0,
            'daily_target'  => $sessionData['daily_target']  ?? 3
        ];

        return view('game.result', compact('user', 'lesson', 'progress', 'nextLesson', 'result'));
    }
}
