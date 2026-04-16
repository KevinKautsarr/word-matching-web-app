<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil user.
     * Sertakan riwayat XP dari user_xp_logs.
     */
    public function index(): View
    {
        $user = Auth::user();

        // Ambil riwayat XP terbaru (10 entri)
        $xpLogs = $user->xpLogs()
            ->latest()
            ->take(10)
            ->get();

        // Statistik ringkasan
        $stats = $this->getUserStats($user);

        return view('profile.index', compact('user', 'xpLogs', 'stats'));
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validated();

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        return redirect()->route('profile.index')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    // =====================================================================
    // PRIVATE HELPERS
    // =====================================================================

    /**
     * Hitung statistik ringkasan untuk halaman profil.
     *
     * @param  \App\Models\User  $user
     * @return array
     */
    private function getUserStats($user): array
    {
        $totalCompleted = $user->progress()->completed()->count();
        $totalXpEarned  = $user->xpLogs()->sum('xp_gained');
        $totalAttempts  = $user->progress()->sum('attempts');

        $wordsLearned = \App\Models\Vocabulary::whereHas('lesson.progress', function($q) use ($user) {
            $q->where('user_id', (int) $user->id)->where('is_completed', true);
        })->count();

        // Hitung XP yang dibutuhkan untuk level berikutnya
        $xpPerLevel     = 100;
        $currentLevelXp = ($user->level - 1) * $xpPerLevel;
        $xpProgress     = $user->xp - $currentLevelXp;
        $xpToNextLevel  = $xpPerLevel - ($user->xp % $xpPerLevel);

        return [
            'total_completed' => (int) ($totalCompleted ?? 0),
            'total_xp_earned' => (int) ($totalXpEarned ?? 0),
            'total_attempts'  => (int) ($totalAttempts ?? 0),
            'xp_progress'     => (int) ($xpProgress ?? 0),
            'xp_to_next_level'=> (int) ($xpToNextLevel ?? 100),
            'xp_percent'      => min(100, (int) round(($user->xp % $xpPerLevel) / $xpPerLevel * 100)),
            'words_learned'   => (int) ($wordsLearned ?? 0),
        ];
    }
}
