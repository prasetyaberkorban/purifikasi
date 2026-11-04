<x-layouts.guest title="Daftar Akun">
    <form method="POST" action="{{ route('register.store') }}" class="space-y-4"
          x-data="{
              password: '',
              password_confirmation: '',
              get mismatch() {
                  return this.password_confirmation.length > 0 && this.password !== this.password_confirmation;
              },
              get tooShort() {
                  return this.password.length > 0 && this.password.length < 8;
              },
              handleSubmit(e) {
                  if (this.password.length < 8) {
                      e.preventDefault();
                      return;
                  }
                  if (this.password !== this.password_confirmation) {
                      e.preventDefault();
                      return;
                  }
              }
          }"
          @submit="handleSubmit($event)">
        @csrf

        <div>
            <label for="name" class="label">Nama Lengkap</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                   autocomplete="name" class="field @error('name') border-red-500 ring-1 ring-red-500 @enderror" placeholder="Nama lengkap">
            @error('name')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="username" class="label">Username</label>
            <input id="username" name="username" type="text" value="{{ old('username') }}" required
                   autocomplete="username" class="field @error('username') border-red-500 ring-1 ring-red-500 @enderror" placeholder="username"
                   pattern="[a-z0-9._-]+" title="Huruf kecil, angka, titik, garis bawah, atau tanda hubung.">
            <p class="mt-1 text-[0.7rem] text-ink-soft">Huruf kecil, angka, titik, garis bawah, atau tanda hubung.</p>
            @error('username')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                   autocomplete="email" class="field @error('email') border-red-500 ring-1 ring-red-500 @enderror" placeholder="nama@purifikasi.co.id">
            @error('email')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="label">Kata Sandi</label>
            <input id="password" name="password" type="password" required autocomplete="new-password"
                   minlength="8"
                   x-model="password"
                   class="field @error('password') border-red-500 ring-1 ring-red-500 @enderror"
                   placeholder="Minimal 8 karakter">
            <template x-if="tooShort">
                <p class="mt-1 text-xs font-medium text-red-600">Kata sandi minimal harus 8 karakter.</p>
            </template>
            @error('password')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="label">Konfirmasi Kata Sandi</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                   autocomplete="new-password"
                   minlength="8"
                   x-model="password_confirmation"
                   class="field @error('password_confirmation') border-red-500 ring-1 ring-red-500 @enderror"
                   placeholder="Ulangi kata sandi">
            <template x-if="mismatch">
                <div class="mt-1.5 flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">
                    <x-ui-icon name="warning" class="h-3.5 w-3.5 shrink-0 text-red-600" />
                    <span>Kata sandi tidak sama</span>
                </div>
            </template>
            @if ($errors->has('password') && str_contains($errors->first('password'), 'tidak sama'))
                <div class="mt-1.5 flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">
                    <x-ui-icon name="warning" class="h-3.5 w-3.5 shrink-0 text-red-600" />
                    <span>Kata sandi tidak sama</span>
                </div>
            @endif
        </div>

        <button type="submit" class="btn-primary w-full">Daftar Sekarang</button>
    </form>

    <p class="mt-6 text-center text-sm text-ink-soft">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-semibold text-leaf hover:underline">Masuk</a>
    </p>
</x-layouts.guest>