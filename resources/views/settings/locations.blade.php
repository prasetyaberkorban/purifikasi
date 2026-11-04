<x-app-layout title="Lokasi" subtitle="Tempat penyimpanan dan penempatan aset" active="locations">
    <div class="grid gap-4 lg:grid-cols-3">
        <section class="card overflow-hidden lg:col-span-2">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h2 class="text-sm font-bold text-ink">Daftar Lokasi</h2>
                <p class="text-[0.72rem] text-ink-soft">{{ $locations->count() }} lokasi terdaftar.</p>
            </div>

            <ul class="divide-y divide-line">
                @forelse ($locations as $location)
                    <li class="px-4 py-4 sm:px-5" x-data="{ edit: false }">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="flex items-center gap-2 font-semibold text-ink">
                                    <x-ui-icon name="pin" class="h-4 w-4 text-leaf" />
                                    {{ $location->name }}
                                    @if ($location->city)
                                        <span class="badge-muted">{{ $location->city }}</span>
                                    @endif
                                </p>
                                <p class="mt-0.5 text-[0.72rem] text-ink-soft">{{ $location->address ?? 'Alamat belum diisi' }}</p>
                                <p class="mt-0.5 text-[0.7rem] text-ink-soft">
                                    PJ: {{ $location->pic_name ?? '-' }}{{ $location->pic_phone ? ' · '.$location->pic_phone : '' }}
                                    &middot; {{ $location->assets_count }} aset
                                </p>
                            </div>
                            <div class="flex items-center gap-1">
                                <button type="button" class="rounded-lg p-2 text-ink-soft hover:bg-canvas hover:text-forest" @click="edit = !edit" aria-label="Ubah {{ $location->name }}">
                                    <x-ui-icon name="pencil" class="h-4 w-4" />
                                </button>
                                <form method="POST" action="{{ route('locations.destroy', $location) }}" onsubmit="return confirm('Hapus lokasi {{ $location->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg p-2 text-ink-soft hover:bg-red-50 hover:text-red-600" aria-label="Hapus {{ $location->name }}">
                                        <x-ui-icon name="x" class="h-4 w-4" />
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div x-show="edit" x-cloak class="mt-3 rounded-xl border border-line bg-canvas p-3">
                            <form method="POST" action="{{ route('locations.update', $location) }}" class="grid gap-2.5 sm:grid-cols-2">
                                @csrf
                                @method('PUT')
                                <div>
                                    <label class="label">Nama Lokasi</label>
                                    <input name="name" value="{{ $location->name }}" required class="field">
                                </div>
                                <div>
                                    <label class="label">Kota</label>
                                    <input name="city" value="{{ $location->city }}" class="field">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="label">Alamat</label>
                                    <input name="address" value="{{ $location->address }}" class="field">
                                </div>
                                <div>
                                    <label class="label">Penanggung Jawab</label>
                                    <input name="pic_name" value="{{ $location->pic_name }}" class="field">
                                </div>
                                <div>
                                    <label class="label">Telepon PJ</label>
                                    <input name="pic_phone" value="{{ $location->pic_phone }}" class="field">
                                </div>
                                <div class="flex justify-end gap-2 sm:col-span-2">
                                    <button type="button" class="btn-ghost" @click="edit = false">Batal</button>
                                    <button type="submit" class="btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </li>
                @empty
                    <li class="px-4 py-10 text-center text-sm text-ink-soft">Belum ada lokasi.</li>
                @endforelse
            </ul>
        </section>

        <section class="card h-fit p-4 sm:p-5">
            <h2 class="text-sm font-bold text-ink">Tambah Lokasi</h2>
            <p class="mb-4 text-[0.72rem] text-ink-soft">Lokasi membantu memisahkan gudang, kantor, dan workshop.</p>

            <form method="POST" action="{{ route('locations.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label for="name" class="label">Nama Lokasi <span class="text-red-500">*</span></label>
                    <input id="name" name="name" value="{{ old('name') }}" required class="field" placeholder="Gudang Utama">
                </div>
                <div>
                    <label for="city" class="label">Kota</label>
                    <input id="city" name="city" value="{{ old('city') }}" class="field" placeholder="Depok">
                </div>
                <div>
                    <label for="address" class="label">Alamat</label>
                    <input id="address" name="address" value="{{ old('address') }}" class="field" placeholder="Jl. Margonda Raya No. 12">
                </div>
                <div>
                    <label for="pic_name" class="label">Penanggung Jawab</label>
                    <input id="pic_name" name="pic_name" value="{{ old('pic_name') }}" class="field">
                </div>
                <div>
                    <label for="pic_phone" class="label">Telepon PJ</label>
                    <input id="pic_phone" name="pic_phone" value="{{ old('pic_phone') }}" class="field">
                </div>
                <button type="submit" class="btn-primary w-full">
                    <x-ui-icon name="plus" class="h-4 w-4" /> Simpan Lokasi
                </button>
            </form>
        </section>
    </div>
</x-app-layout>