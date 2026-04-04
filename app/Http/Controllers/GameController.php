<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\UserProgress;
use App\Models\UserXpLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GameController extends Controller
{
    /**
     * Level threshold XP per level.
     * Level naik setiap kelipatan 100 XP.
     */
    private const XP_PER_LEVEL = 100;

    /**
     * Minimal skor (%) agar lesson dianggap selesai.
     */
    private const PASS_SCORE_PERCENT = 70;

    // =====================================================================
    // PLAY
    // =====================================================================

    /**
     * Tampilkan halaman permainan Word Matching untuk sebuah lesson.
     */
    public function play(int $lesson_id): View|RedirectResponse
    {
        $lesson = Lesson::with('vocabularies')->findOrFail($lesson_id);
        $user   = Auth::user();

        if ($lesson->vocabularies->isEmpty()) {
            return redirect()->route('lessons.show', $lesson_id)
                ->with('error', 'Lesson ini belum memiliki kosakata. Coba lagi nanti.');
        }

        // Acak urutan vocabulary untuk variasi soal
        $vocabularies = $lesson->vocabularies->shuffle();

        return view('game.play', compact('lesson', 'vocabularies', 'user'));
    }

    // =====================================================================
    // SUBMIT
    // =====================================================================

    /**
     * Proses pengumpulan jawaban game Word Matching.
     * Hitung skor, update progress, dan berikan XP jika lulus.
     */
    public function submit(Request $request, int $lesson_id): RedirectResponse
    {
        $request->validate([
            'answers'    => ['required', 'array'],
            'answers.*'  => ['nullable', 'integer'],
            'time_spent' => ['required', 'integer', 'min:0'],
        ]);

        $lesson = Lesson::with('vocabularies')->findOrFail($lesson_id);
        $user   = Auth::user();

        // Hitung skor
        $score        = $this->calculateScore($request->answers, $lesson);
        $total        = $lesson->vocabularies->count();
        $scorePercent = $total > 0 ? (int) round(($score / $total) * 100) : 0;
        $isPassed     = $scorePercent >= self::PASS_SCORE_PERCENT;

        // Simpan / update progress
        $progress = $this->upsertProgress($user, $lesson, $scorePercent, $isPassed, $request->time_spent);

        // Berikan XP hanya jika lulus DAN pertama kali atau improve score
        if ($isPassed) {
            $this->grantXp($user, $lesson, $progress);
        }

        // Simpan hasil ke session untuk halaman result
        session([
            'game_result' => [
                'lesson_id'     => $lesson_id,
                'score'         => $scorePercent,
                'correct'       => $score,
                'total'         => $total,
                'is_passed'     => $isPassed,
                'xp_gained'     => $isPassed ? $lesson->xp_reward : 0,
                'time_spent'    => $request->time_spent,
                'attempts'      => $progress->attempts,
            ],
        ]);

        return redirect()->route('game.result', $lesson_id);
    }

    // =====================================================================
    // RESULT
    // =====================================================================

    /**
     * Tampilkan halaman hasil permainan.
     */
    public function result(int $lesson_id): View|RedirectResponse
    {
        $result = session('game_result');

        if (! $result || $result['lesson_id'] !== $lesson_id) {
            return redirect()->route('lessons.show', $lesson_id)
                ->with('error', 'Tidak ada hasil permainan yang ditemukan.');
        }

        $lesson = Lesson::with('unit')->findOrFail($lesson_id);
        $user   = Auth::user();

        return view('game.result', compact('lesson', 'result', 'user'));
    }

    // =====================================================================
    // PRIVATE HELPERS
    // =====================================================================

    /**
     * Hitung jumlah jawaban benar dari input user.
     *
     * Format answers: ['vocabulary_id' => 'matched_vocabulary_id']
     * Jawaban benar: vocabulary_id == matched_vocabulary_id (pasangan dirinya sendiri)
     *
     * @param  array   $answers   Array jawaban dari form
     * @param  Lesson  $lesson    Lesson yang sedang dikerjakan
     * @return int                Jumlah jawaban benar
     */
    private function calculateScore(array $answers, Lesson $lesson): int
    {
        $correct = 0;

        foreach ($answers as $vocabId => $matchedId) {
            // Jawaban benar = vocab dipasangkan dengan id-nya sendiri
            if ((int) $vocabId === (int) $matchedId) {
                $correct++;
            }
        }

        return $correct;
    }

    /**
     * Buat atau perbarui record UserProgress.
     * Simpan score terbaik, increment attempts.
     */
    private function upsertProgress($user, Lesson $lesson, int $scorePercent, bool $isPassed, int $timeSpent): UserProgress
    {
        $progress = UserProgress::firstOrNew([
            'user_id'   => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        // Simpan score terbaik
        $progress->score        = max($progress->score ?? 0, $scorePercent);
        $progress->is_completed = $progress->is_completed || $isPassed;
        $progress->time_spent   = $timeSpent;
        $progress->attempts     = ($progress->attempts ?? 0) + 1;
        $progress->save();

        return $progress;
    }

    /**
     * Berikan XP ke user dan catat ke user_xp_logs.
     * Juga periksa apakah user naik level.
     */
    private function grantXp($user, Lesson $lesson, UserProgress $progress): void
    {
        // Hanya beri XP pada attempt pertama yang lulus (atau jika belum pernah dapat XP)
        $alreadyRewarded = UserXpLog::where('user_id', $user->id)
            ->where('activity', 'lesson_completed:' . $lesson->id)
            ->exists();

        if ($alreadyRewarded) {
            return;
        }

        $xpGained = $lesson->xp_reward;

        // Catat riwayat XP
        UserXpLog::create([
            'user_id'   => $user->id,
            'xp_gained' => $xpGained,
            'activity'  => 'lesson_completed:' . $lesson->id,
        ]);

        // Update XP user
        $user->xp += $xpGained;

        // Cek naik level
        $user->level = $this->calculateLevel($user->xp);

        $user->save();
    }

    /**
     * Hitung level berdasarkan total XP.
     * Setiap kelipatan XP_PER_LEVEL = 1 level.
     */
    private function calculateLevel(int $totalXp): int
    {
        return (int) floor($totalXp / self::XP_PER_LEVEL) + 1;
    }
}
