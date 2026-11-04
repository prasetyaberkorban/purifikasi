<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Masuk memakai username atau email (keduanya diterima pada satu kolom).
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'login' => ['required', 'string', 'max:160'],
            'password' => ['required', 'string'],
        ]);

        $login = trim($data['login']);
        $needle = Str::lower($login);
        $key = $needle.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'login' => 'Terlalu banyak percobaan masuk. Coba lagi dalam '.RateLimiter::availableIn($key).' detik.',
            ]);
        }

        // Pencocokan tanpa membedakan huruf besar/kecil untuk username maupun email.
        $user = User::query()
            ->where(fn ($query) => $query
                ->whereRaw('lower(username) = ?', [$needle])
                ->orWhereRaw('lower(email) = ?', [$needle]))
            ->first();

        if (! $user || ! $user->is_active || ! Hash::check($data['password'], $user->password)) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'login' => 'Username/email atau kata sandi salah, atau akun tidak aktif.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        RateLimiter::clear($key);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
