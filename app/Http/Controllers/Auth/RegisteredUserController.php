<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Pendaftaran mandiri selalu menghasilkan akun Karyawan;
     * peran Admin hanya dapat diberikan oleh Admin lewat dashboard.
     */
    public function store(Request $request): RedirectResponse
    {
        // Email & username disimpan huruf kecil agar aturan unik dan proses masuk konsisten.
        $request->merge([
            'username' => Str::lower(trim((string) $request->input('username'))),
            'email' => Str::lower(trim((string) $request->input('email'))),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'min:3', 'max:64', 'regex:/^[a-z0-9._-]+$/', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:160', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.min' => 'Kata sandi minimal harus 8 karakter.',
            'password.confirmed' => 'Kata sandi tidak sama',
            'username.unique' => 'Username sudah terdaftar.',
            'email.unique' => 'Email sudah terdaftar.',
        ]);

        $user = User::query()->create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => UserRole::Karyawan,
            'is_active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('status', 'Akun berhasil dibuat. Selamat datang, '.$user->name.'!');
    }
}
