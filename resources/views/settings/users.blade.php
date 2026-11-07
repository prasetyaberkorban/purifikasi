<x-app-layout title="Pengaturan Pengguna" subtitle="Kelola akun dan hak akses sistem inventaris" active="users">
    <div class="grid gap-4 lg:grid-cols-3">
        <section class="card overflow-hidden lg:col-span-2">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h2 class="text-sm font-bold text-ink">Daftar Pengguna</h2>
                <p class="text-[0.72rem] text-ink-soft">{{ $users->count() }} akun terdaftar.</p>
            </div>

            <ul class="divide-y divide-line">
                @foreach ($users as $user)
                    <li class="px-4 py-4 sm:px-5" x-data="{ edit: false }">
                        <div class="flex items-start gap-3">
                            <img src="{{ $user->avatarUrl() }}" alt="" class="h-11 w-11 shrink-0 rounded-xl object-cover ring-1 ring-line">
                            <div class="min-w-0 flex-1">
                                <p class="flex flex-wrap items-center gap-2 font-semibold text-ink">
                                    {{ $user->name }}
                                    <span class="badge-leaf">{{ $user->role->label() }}</span>
                                    @unless ($user->is_active)
                                        <span class="badge-danger">Nonaktif</span>
                                    @endunless
                                    @if ($user->is(auth()->user()))
                                        <span class="badge-muted">Anda</span>
                                    @endif
                                </p>
                                <p class="text-[0.72rem] text-ink-soft">{{ '@'.$user->username }} &middot; {{ $user->email }}</p>
                                <p class="text-[0.7rem] text-ink-soft">
                                    {{ $user->unit ?? 'Unit belum diisi' }}{{ $user->phone ? ' · '.$user->phone : '' }}
                                    &middot; {{ $user->loans()->count() }} peminjaman
                                </p>
                            </div>
                            <div class="flex items-center gap-1">
                                <button type="button" class="rounded-lg p-2 text-ink-soft hover:bg-canvas hover:text-forest" @click="edit = !edit" aria-label="Ubah {{ $user->name }}">
                                    <x-ui-icon name="pencil" class="h-4 w-4" />
                                </button>
                                @unless ($user->is(auth()->user()))
                                    <form method="POST" action="{{ route('users.destroy', $user) }}"
                                          data-confirm-title="Hapus Pengguna?"
                                          data-confirm-delete="Apakah Anda yakin ingin menghapus akun pengguna {{ $user->name }}? Data yang dihapus tidak dapat dipulihkan.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg p-2 text-ink-soft hover:bg-red-50 hover:text-red-600" aria-label="Hapus {{ $user->name }}">
                                            <x-ui-icon name="x" class="h-4 w-4" />
                                        </button>
                                    </form>
                                @endunless
                            </div>
                        </div>

                        <div x-show="edit" x-cloak class="mt-3 rounded-xl border border-line bg-canvas p-3">
                            <form method="POST" action="{{ route('users.update', $user) }}" class="grid gap-2.5 sm:grid-cols-2">
                                @csrf
                                @method('PUT')
                                <div>
                                    <label class="label">Nama</label>
                                    <input name="name" value="{{ $user->name }}" required class="field">
                                </div>
                                <div>
                                    <label class="label">Username</label>
                                    <input name="username" value="{{ $user->username }}" required class="field">
                                </div>
                                <div>
                                    <label class="label">Email</label>
                                    <input name="email" type="email" value="{{ $user->email }}" required class="field">
                                </div>
                                <div>
                                    <label class="label">Peran</label>
                                    <select name="role" class="field">
                                        @foreach ($roles as $value => $label)
                                            <option value="{{ $value }}" @selected($user->role->value === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="label">Unit</label>
                                    <input name="unit" value="{{ $user->unit }}" class="field">
                                </div>
                                <div>
                                    <label class="label">Telepon</label>
                                    <input name="phone" value="{{ $user->phone }}" class="field">
                                </div>
                                <div>
                                    <label class="label">Kata Sandi Baru (opsional)</label>
                                    <input name="password" type="password" class="field" placeholder="Kosongkan bila tidak diganti">
                                </div>
                                <label class="flex items-center gap-2 text-[0.78rem] text-ink-soft sm:col-span-2">
                                    <input type="checkbox" name="is_active" value="1" @checked($user->is_active) class="h-4 w-4 rounded border-line text-leaf focus:ring-leaf/30">
                                    Akun aktif (dapat masuk ke sistem)
                                </label>
                                <div class="flex justify-end gap-2 sm:col-span-2">
                                    <button type="button" class="btn-ghost" @click="edit = false">Batal</button>
                                    <button type="submit" class="btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>

        <div class="space-y-4">
            <section class="card p-4 sm:p-5">
                <h2 class="text-sm font-bold text-ink">Tambah Pengguna</h2>
                <p class="mb-4 text-[0.72rem] text-ink-soft">Tentukan peran sesuai tanggung jawab.</p>

                <form method="POST" action="{{ route('users.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label for="name" class="label">Nama <span class="text-red-500">*</span></label>
                        <input id="name" name="name" value="{{ old('name') }}" required class="field">
                    </div>
                    <div>
                        <label for="username" class="label">Username <span class="text-red-500">*</span></label>
                        <input id="username" name="username" value="{{ old('username') }}" required class="field"
                               pattern="[a-z0-9._-]+" title="Huruf kecil, angka, titik, garis bawah, atau tanda hubung.">
                    </div>
                    <div>
                        <label for="email" class="label">Email <span class="text-red-500">*</span></label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required class="field">
                    </div>
                    <div>
                        <label for="role" class="label">Peran <span class="text-red-500">*</span></label>
                        <select id="role" name="role" required class="field">
                            @foreach ($roles as $value => $label)
                                <option value="{{ $value }}" @selected(old('role') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="unit" class="label">Unit</label>
                        <input id="unit" name="unit" value="{{ old('unit') }}" class="field">
                    </div>
                    <div>
                        <label for="phone" class="label">Telepon</label>
                        <input id="phone" name="phone" value="{{ old('phone') }}" class="field">
                    </div>
                    <div>
                        <label for="password" class="label">Kata Sandi <span class="text-red-500">*</span></label>
                        <input id="password" name="password" type="password" required class="field" placeholder="Minimal 8 karakter">
                    </div>
                    <button type="submit" class="btn-primary w-full">
                        <x-ui-icon name="plus" class="h-4 w-4" /> Simpan Pengguna
                    </button>
                </form>
            </section>

            <section class="card p-4 sm:p-5">
                <h2 class="mb-2 text-sm font-bold text-ink">Hak Akses</h2>
                <ul class="space-y-2.5 text-[0.75rem] text-ink-soft">
                    @foreach (\App\Enums\UserRole::cases() as $role)
                        <li>
                            <p class="font-semibold text-ink">{{ $role->label() }}</p>
                            <p>{{ $role->description() }}</p>
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>
    </div>
</x-app-layout>