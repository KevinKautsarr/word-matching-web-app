<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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

    /**
     * Proses update data profil user.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'current_password'      => ['nullable', 'string'],
            'password'              => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Jika user ingin ganti password
        if (filled($request->password)) {
            if (! Hash::check($request->current_password, $user->password)) {
                return back()->withErrors([
                    'current_password' => 'Password saat ini tidak sesuai.',
                ]);
            }

            $user->password = Hash::make($request->password);
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

        // Hitung XP yang dibutuhkan untuk level berikutnya
        $xpPerLevel     = 100;
        $currentLevelXp = ($user->level - 1) * $xpPerLevel;
        $xpProgress     = $user->xp - $currentLevelXp;
        $xpToNextLevel  = $xpPerLevel - ($user->xp % $xpPerLevel);

        return [
            'total_completed' => $totalCompleted,
            'total_xp_earned' => $totalXpEarned,
            'total_attempts'  => $totalAttempts,
            'xp_progress'     => $xpProgress,
            'xp_to_next_level'=> $xpToNextLevel,
            'xp_percent'      => min(100, (int) round(($user->xp % $xpPerLevel) / $xpPerLevel * 100)),
        ];
    }
}
