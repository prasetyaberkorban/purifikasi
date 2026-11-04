<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email,'.$user->id],
            'unit' => ['nullable', 'string', 'max:64'],
            'phone' => ['nullable', 'string', 'max:32'],
        ]);

        $user->update($data);

        return back()->with('status', 'Profil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($data['current_password'], $request->user()->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak sesuai.']);
        }

        $request->user()->update(['password' => $data['password']]);

        return back()->with('status', 'Kata sandi diperbarui.');
    }

    public function notifications(Request $request): View
    {
        $critical = Asset::query()
            ->with(['category', 'location', 'items'])
            ->whereRaw('(quantity - borrowed_quantity) <= minimum_stock')
            ->orderByRaw('(quantity - borrowed_quantity) asc')
            ->limit(20)
            ->get();

        $unchecked = Asset::query()
            ->with(['category', 'location', 'items'])
            ->whereNull('last_checked_at')
            ->orderBy('name')
            ->limit(10)
            ->get();

        return view('notifications.index', [
            'critical' => $critical,
            'unchecked' => $unchecked,
            'threshold' => (int) Setting::get('low_stock_threshold', '3'),
        ]);
    }
}