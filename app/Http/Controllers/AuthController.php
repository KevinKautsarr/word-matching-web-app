<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    // =====================================================================
    // REGISTER
    // =====================================================================

    /**
     * Tampilkan halaman registrasi.
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Proses registrasi user baru.
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Selamat datang di LEXORA, ' . $user->name . '!');
    }

    // =====================================================================
    // LOGIN
    // =====================================================================

    /**
     * Tampilkan halaman login.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Proses login user.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah.']);
        }

        $request->session()->regenerate();

        $this->updateStreak(Auth::user());

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
    }

    // =====================================================================
    // LOGOUT
    // =====================================================================

    /**
     * Proses logout user.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Kamu berhasil logout.');
    }

    // =====================================================================
    // PRIVATE HELPERS
    // =====================================================================

    /**
     * Perbarui streak login harian user.
     * - Jika terakhir aktif adalah kemarin → streak +1
     * - Jika terakhir aktif adalah hari ini → streak tetap
     * - Selain itu → reset streak ke 1
     */
    private function updateStreak(User $user): void
    {
        $today     = Carbon::today();
        $lastActive = $user->last_active_at ? Carbon::parse($user->last_active_at)->startOfDay() : null;

        if ($lastActive === null) {
            // Login pertama kali
            $user->streak = 1;
        } elseif ($lastActive->eq($today)) {
            // Sudah login hari ini, tidak perlu update
            return;
        } elseif ($lastActive->eq($today->copy()->subDay())) {
            // Login kemarin → lanjutkan streak
            $user->streak += 1;
        } else {
            // Lewat lebih dari 1 hari → reset
            $user->streak = 1;
        }

        $user->last_active_at = Carbon::now();
        $user->save();
    }
}
