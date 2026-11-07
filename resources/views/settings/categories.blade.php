<x-app-layout title="Kategori" subtitle="Kelompokkan aset berdasarkan jenisnya" active="categories">
    <div class="grid gap-4 lg:grid-cols-3">
        <section class="card overflow-hidden lg:col-span-2">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h2 class="text-sm font-bold text-ink">Daftar Kategori</h2>
                <p class="text-[0.72rem] text-ink-soft">{{ $categories->count() }} kategori terdaftar.</p>
            </div>

            <div class="table-wrap">
                <table class="data-table min-w-[38rem]">
                    <thead>
                        <tr>
                            <th scope="col">Kode</th>
                            <th scope="col">Nama</th>
                            <th scope="col" class="text-right">Jumlah Aset</th>
                            <th scope="col" class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>
                                    <span class="inline-flex items-center gap-2">
                                        <span class="h-2.5 w-2.5 rounded-full" style="background: {{ $category->color }}"></span>
                                        <span class="font-mono text-[0.72rem] font-semibold text-ink">{{ $category->code }}</span>
                                    </span>
                                </td>
                                <td>
                                    <p class="font-semibold text-ink">{{ $category->name }}</p>
                                    <p class="text-[0.68rem] text-ink-soft">{{ $category->description }}</p>
                                </td>
                                <td class="text-right text-ink-soft">{{ $category->assets_count }}</td>
                                <td>
                                    <div class="flex items-center justify-end gap-1" x-data="{ edit: false }">
                                        <button type="button" class="rounded-lg p-2 text-ink-soft hover:bg-canvas hover:text-forest" @click="edit = !edit" aria-label="Ubah {{ $category->name }}">
                                            <x-ui-icon name="pencil" class="h-4 w-4" />
                                        </button>
                                        <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                              data-confirm-title="Hapus Kategori?"
                                              data-confirm-delete="Apakah Anda yakin ingin menghapus kategori {{ $category->name }}? Data yang dihapus tidak dapat dipulihkan.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg p-2 text-ink-soft hover:bg-red-50 hover:text-red-600" aria-label="Hapus {{ $category->name }}">
                                                <x-ui-icon name="x" class="h-4 w-4" />
                                            </button>
                                        </form>

                                        <div x-show="edit" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-forest-900/50 p-4">
                                            <div class="w-full max-w-md rounded-2xl bg-white p-5" @click.outside="edit = false">
                                                <h3 class="text-sm font-bold text-ink">Ubah Kategori</h3>
                                                <form method="POST" action="{{ route('categories.update', $category) }}" class="mt-3 space-y-3">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="grid grid-cols-3 gap-2">
                                                        <div>
                                                            <label class="label">Kode</label>
                                                            <input name="code" value="{{ $category->code }}" required class="field font-mono uppercase">
                                                        </div>
                                                        <div class="col-span-2">
                                                            <label class="label">Nama</label>
                                                            <input name="name" value="{{ $category->name }}" required class="field">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label class="label">Deskripsi</label>
                                                        <input name="description" value="{{ $category->description }}" class="field">
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <label class="label mb-0">Warna</label>
                                                        <input type="color" name="color" value="{{ $category->color }}" class="h-9 w-14 rounded-lg border border-line">
                                                    </div>
                                                    <div class="flex justify-end gap-2 pt-1">
                                                        <button type="button" class="btn-ghost" @click="edit = false">Batal</button>
                                                        <button type="submit" class="btn-primary">Simpan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="card h-fit p-4 sm:p-5">
            <h2 class="text-sm font-bold text-ink">Tambah Kategori</h2>
            <p class="mb-4 text-[0.72rem] text-ink-soft">Kode kategori dipakai dalam pembentukan kode aset.</p>

            <form method="POST" action="{{ route('categories.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label for="code" class="label">Kode <span class="text-red-500">*</span></label>
                    <input id="code" name="code" value="{{ old('code') }}" required maxlength="24" class="field font-mono uppercase" placeholder="PL">
                </div>
                <div>
                    <label for="name" class="label">Nama <span class="text-red-500">*</span></label>
                    <input id="name" name="name" value="{{ old('name') }}" required class="field" placeholder="Peralatan Lapangan">
                </div>
                <div>
                    <label for="description" class="label">Deskripsi</label>
                    <input id="description" name="description" value="{{ old('description') }}" class="field" placeholder="Keterangan singkat">
                </div>
                <div class="flex items-center gap-3">
                    <label for="color" class="label mb-0">Warna</label>
                    <input id="color" type="color" name="color" value="{{ old('color', '#1E6B38') }}" class="h-9 w-14 rounded-lg border border-line">
                </div>
                <button type="submit" class="btn-primary w-full">
                    <x-ui-icon name="plus" class="h-4 w-4" /> Simpan Kategori
                </button>
            </form>
        </section>
    </div>
</x-app-layout>