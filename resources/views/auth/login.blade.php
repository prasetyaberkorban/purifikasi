<x-layouts.guest title="Masuk">
    <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
        @csrf

        <div>
            <label for="login" class="label">Username/Email</label>
            <input id="login" name="login" type="text" value="{{ old('login') }}" required autofocus
                   autocomplete="username" class="field @error('login') border-red-500 ring-1 ring-red-500 @enderror" placeholder="Username atau email">
            @error('login')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="label">Kata Sandi</label>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                   class="field @error('password') border-red-500 ring-1 ring-red-500 @enderror" placeholder="••••••••">
            @error('password')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex items-center gap-2.5 text-sm text-ink-soft">
            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-line text-leaf focus:ring-leaf/30">
            Ingat saya di perangkat ini
        </label>

        <button type="submit" class="btn-primary w-full">Masuk ke Dashboard</button>
    </form>

    <p class="mt-6 text-center text-sm text-ink-soft">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-semibold text-leaf hover:underline">Daftar Akun</a>
    </p>
</x-layouts.guest>
