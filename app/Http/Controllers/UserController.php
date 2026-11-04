<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(): View
    {
        return view('settings.users', [
            'users' => User::query()->orderBy('name')->get(),
            'roles' => UserRole::options(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'username' => Str::lower(trim((string) $request->input('username'))),
            'email' => Str::lower(trim((string) $request->input('email'))),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'min:3', 'max:64', 'regex:/^[a-z0-9._-]+$/', 'unique:users,username'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'unit' => ['nullable', 'string', 'max:64'],
            'phone' => ['nullable', 'string', 'max:32'],
            'password' => ['required', 'string', Password::min(8)],
        ]);

        User::query()->create($data + ['is_active' => true]);

        return back()->with('status', "Pengguna {$data['name']} ditambahkan.");
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->merge([
            'username' => Str::lower(trim((string) $request->input('username'))),
            'email' => Str::lower(trim((string) $request->input('email'))),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'min:3', 'max:64', 'regex:/^[a-z0-9._-]+$/', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', 'max:160', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::enum(UserRole::class)],
            'unit' => ['nullable', 'string', 'max:64'],
            'phone' => ['nullable', 'string', 'max:32'],
            'password' => ['nullable', 'string', Password::min(8)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $data['is_active'] = $request->boolean('is_active');

        if ($user->is($request->user()) && ! $data['is_active']) {
            return back()->withErrors(['is_active' => 'Anda tidak dapat menonaktifkan akun sendiri.']);
        }

        $user->update($data);

        return back()->with('status', "Pengguna {$user->name} diperbarui.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->withErrors(['user' => 'Anda tidak dapat menghapus akun sendiri.']);
        }

        if ($user->loans()->exists()) {
            return back()->withErrors(['user' => 'Pengguna memiliki riwayat peminjaman. Nonaktifkan akun alih-alih menghapus.']);
        }

        $name = $user->name;
        $user->delete();

        return back()->with('status', "Pengguna {$name} dihapus.");
    }
}