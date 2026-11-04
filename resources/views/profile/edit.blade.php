<x-app-layout title="Profil Saya" subtitle="Kelola data akun dan kata sandi" active="profile">
    <div class="grid gap-4 lg:grid-cols-3">
        <section class="card p-4 sm:p-5 lg:col-span-2">
            <div class="flex items-center gap-4">
                <img src="{{ $user->avatarUrl() }}" alt="" class="h-16 w-16 rounded-2xl object-cover ring-1 ring-line">
                <div class="min-w-0">
                    <h2 class="text-lg font-bold text-ink">{{ $user->name }}</h2>
                    <p class="text-[0.78rem] text-ink-soft">&#64;{{ $user->username }} &middot; {{ $user->email }}</p>
                    <span class="badge-leaf mt-1">{{ $user->role->label() }}</span>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="mt-5 grid gap-3.5 sm:grid-cols-2">
                @csrf
                @method('PUT')
                <div>
                    <label for="name" class="label">Nama</label>
                    <input id="name" name="name" value="{{ old('name', $user->name) }}" required class="field">
                </div>
                <div>
                    <label for="email" class="label">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="field">
                </div>
                <div>
                    <label for="unit" class="label">Unit</label>
                    <input id="unit" name="unit" value="{{ old('unit', $user->unit) }}" class="field">
                </div>
                <div>
                    <label for="phone" class="label">Telepon</label>
                    <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="field">
                </div>
                <div class="sm:col-span-2">
                    <button type="submit" class="btn-primary">Simpan Profil</button>
                </div>
            </form>
        </section>

        <div class="space-y-4">
            <section class="card p-4 sm:p-5">
                <h2 class="text-sm font-bold text-ink">Ubah Kata Sandi</h2>
                <p class="mb-4 text-[0.72rem] text-ink-soft">Gunakan minimal 8 karakter.</p>

                <form method="POST" action="{{ route('profile.password') }}" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="current_password" class="label">Kata Sandi Saat Ini</label>
                        <input id="current_password" name="current_password" type="password" required class="field">
                    </div>
                    <div>
                        <label for="password" class="label">Kata Sandi Baru</label>
                        <input id="password" name="password" type="password" required class="field">
                    </div>
                    <div>
                        <label for="password_confirmation" class="label">Konfirmasi Kata Sandi</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required class="field">
                    </div>
                    <button type="submit" class="btn-forest w-full">Perbarui Kata Sandi</button>
                </form>
            </section>

            <section class="card p-4 sm:p-5">
                <h2 class="mb-2 text-sm font-bold text-ink">Aktivitas Saya</h2>
                <dl class="space-y-2 text-[0.78rem]">
                    <div class="flex items-center justify-between">
                        <dt class="text-ink-soft">Total peminjaman</dt>
                        <dd class="font-semibold text-ink">{{ $user->loans()->count() }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-ink-soft">Sedang dipinjam</dt>
                        <dd class="font-semibold text-ink">{{ $user->loans()->active()->count() }}</dd>
                    </div>
                </dl>
                <a href="{{ route('loans.history') }}" class="btn-ghost mt-3 w-full">Lihat Riwayat</a>
            </section>

            <section class="card p-4 sm:p-5">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-ghost w-full text-red-600 hover:border-red-200">
                        <x-ui-icon name="logout" class="h-4 w-4" /> Keluar dari Akun
                    </button>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>