<x-app-layout title="Referensi Pra-Kontrak" subtitle="Rekap Project Pertamina Penawaran" active="pertamina.ref-pra-kontrak">
    <div x-data="{
        showModal: false,
        isEdit: false,
        type: 'tahap-pra-kontrak',
        modalTitle: '',
        formAction: '',
        form: {
            id: null,
            tahap: '',
            bobot: '0.2',
            nama: ''
        },
        openAdd(type) {
            this.isEdit = false;
            this.type = type;
            this.formAction = '/manajemen-proyek/pertamina/referensi/' + type;
            this.form = { id: null, tahap: '', bobot: '0.2', nama: '' };

            if (type === 'tahap-pra-kontrak') {
                this.modalTitle = '+ Add Tahap Pra-Kontrak';
            } else if (type === 'hasil-penawaran') {
                this.modalTitle = '+ Add Hasil Penawaran';
            } else {
                this.modalTitle = '+ Add Status Proses';
            }
            this.showModal = true;
        },
        openEdit(type, item) {
            this.isEdit = true;
            this.type = type;
            this.formAction = '/manajemen-proyek/pertamina/referensi/' + type + '/' + item.id;
            this.form = {
                id: item.id,
                tahap: item.tahap || '',
                bobot: item.bobot !== undefined ? parseFloat(item.bobot).toString() : '0.2',
                nama: item.nama || ''
            };

            if (type === 'tahap-pra-kontrak') {
                this.modalTitle = 'Edit Tahap Pra-Kontrak: ' + item.tahap;
            } else if (type === 'hasil-penawaran') {
                this.modalTitle = 'Edit Hasil Penawaran: ' + item.nama;
            } else {
                this.modalTitle = 'Edit Status Proses: ' + item.nama;
            }
            this.showModal = true;
        }
    }">

        {{-- Navigasi Tab Sub Bab 3 --}}
        @include('pertamina._tabs', ['active' => 'ref-pra-kontrak'])

        {{-- Flash Notification --}}
        @if (session('status'))
            <div class="mb-4 flex items-center justify-between rounded-xl bg-leaf-50 border border-leaf/30 px-4 py-3 text-xs font-semibold text-forest shadow-xs">
                <div class="flex items-center gap-2">
                    <x-ui-icon name="check" class="h-4 w-4 text-leaf" />
                    <span>{{ session('status') }}</span>
                </div>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Tabel 1: Tahap Pra-Kontrak --}}
            <div class="card overflow-hidden">
                <div class="flex items-center justify-between border-b border-line bg-[#f2f5f3] px-4 py-3">
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-forest">1. Tahap Pra-Kontrak</h3>
                        <p class="text-[0.68rem] text-ink-soft">Tahapan &amp; bobot progress</p>
                    </div>
                    <button type="button" @click="openAdd('tahap-pra-kontrak')" class="btn-primary py-1.5 px-2.5 text-xs inline-flex items-center gap-1 shadow-xs">
                        <x-ui-icon name="plus" class="h-3.5 w-3.5" />
                        <span>Add</span>
                    </button>
                </div>
                <div class="table-wrap">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-canvas border-b border-line text-ink-soft text-[0.67rem] font-semibold uppercase">
                            <tr>
                                <th class="px-3 py-2 text-center" style="width: 30px;">No</th>
                                <th class="px-3 py-2">Tahap</th>
                                <th class="px-2 py-2 text-center">Bobot</th>
                                <th class="px-3 py-2 text-right" style="width: 70px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line/60">
                            @forelse ($tahapList as $idx => $t)
                                <tr class="hover:bg-leaf-50/40">
                                    <td class="px-3 py-2 text-center text-ink-soft font-mono">{{ $idx + 1 }}</td>
                                    <td class="px-3 py-2 font-medium text-ink">{{ $t->tahap }}</td>
                                    <td class="px-2 py-2 text-center font-mono font-bold text-forest">
                                        {{ round($t->bobot * 100) }}% <span class="text-[0.65rem] text-ink-soft font-normal">({{ $t->bobot }})</span>
                                    </td>
                                    <td class="px-3 py-2 text-right whitespace-nowrap">
                                        <button type="button" @click="openEdit('tahap-pra-kontrak', {{ json_encode($t) }})" class="p-1 text-ink-soft hover:text-forest" title="Edit">
                                            <x-ui-icon name="pencil" class="h-3.5 w-3.5" />
                                        </button>
                                        <form method="POST" action="{{ route('pertamina.ref.destroy', ['type' => 'tahap-pra-kontrak', 'id' => $t->id]) }}"
                                              data-confirm-delete="Hapus tahap '{{ $t->tahap }}'?" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-ink-soft hover:text-red-600 transition" title="Hapus">
                                                <x-ui-icon name="trash" class="h-3.5 w-3.5" />
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-3 py-4 text-center text-xs text-ink-soft">Belum ada data tahap.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tabel 2: Hasil Penawaran --}}
            <div class="card overflow-hidden">
                <div class="flex items-center justify-between border-b border-line bg-[#f2f5f3] px-4 py-3">
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-forest">2. Hasil Penawaran</h3>
                        <p class="text-[0.68rem] text-ink-soft">Status hasil keputusan penawaran</p>
                    </div>
                    <button type="button" @click="openAdd('hasil-penawaran')" class="btn-primary py-1.5 px-2.5 text-xs inline-flex items-center gap-1 shadow-xs">
                        <x-ui-icon name="plus" class="h-3.5 w-3.5" />
                        <span>Add</span>
                    </button>
                </div>
                <div class="table-wrap">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-canvas border-b border-line text-ink-soft text-[0.67rem] font-semibold uppercase">
                            <tr>
                                <th class="px-3 py-2 text-center" style="width: 30px;">No</th>
                                <th class="px-3 py-2">Hasil Penawaran</th>
                                <th class="px-3 py-2 text-right" style="width: 70px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line/60">
                            @forelse ($hasilList as $idx => $h)
                                <tr class="hover:bg-leaf-50/40">
                                    <td class="px-3 py-2 text-center text-ink-soft font-mono">{{ $idx + 1 }}</td>
                                    <td class="px-3 py-2 font-semibold text-ink">
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs {{ $h->nama === 'Menang' ? 'bg-emerald-100 text-emerald-800' : (in_array(strtolower($h->nama), ['kalah', 'batal', 'tidak lanjut']) ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-800') }}">
                                            {{ $h->nama }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-right whitespace-nowrap">
                                        <button type="button" @click="openEdit('hasil-penawaran', {{ json_encode($h) }})" class="p-1 text-ink-soft hover:text-forest" title="Edit">
                                            <x-ui-icon name="pencil" class="h-3.5 w-3.5" />
                                        </button>
                                        <form method="POST" action="{{ route('pertamina.ref.destroy', ['type' => 'hasil-penawaran', 'id' => $h->id]) }}"
                                              data-confirm-delete="Hapus hasil penawaran '{{ $h->nama }}'?" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-ink-soft hover:text-red-600 transition" title="Hapus">
                                                <x-ui-icon name="trash" class="h-3.5 w-3.5" />
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-3 py-4 text-center text-xs text-ink-soft">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tabel 3: Status Proses --}}
            <div class="card overflow-hidden">
                <div class="flex items-center justify-between border-b border-line bg-[#f2f5f3] px-4 py-3">
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-forest">3. Status Proses</h3>
                        <p class="text-[0.68rem] text-ink-soft">Status alur proses berjalan</p>
                    </div>
                    <button type="button" @click="openAdd('status-proses')" class="btn-primary py-1.5 px-2.5 text-xs inline-flex items-center gap-1 shadow-xs">
                        <x-ui-icon name="plus" class="h-3.5 w-3.5" />
                        <span>Add</span>
                    </button>
                </div>
                <div class="table-wrap">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-canvas border-b border-line text-ink-soft text-[0.67rem] font-semibold uppercase">
                            <tr>
                                <th class="px-3 py-2 text-center" style="width: 30px;">No</th>
                                <th class="px-3 py-2">Status Proses</th>
                                <th class="px-3 py-2 text-right" style="width: 70px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line/60">
                            @forelse ($statusList as $idx => $s)
                                <tr class="hover:bg-leaf-50/40">
                                    <td class="px-3 py-2 text-center text-ink-soft font-mono">{{ $idx + 1 }}</td>
                                    <td class="px-3 py-2 font-medium text-ink">{{ $s->nama }}</td>
                                    <td class="px-3 py-2 text-right whitespace-nowrap">
                                        <button type="button" @click="openEdit('status-proses', {{ json_encode($s) }})" class="p-1 text-ink-soft hover:text-forest" title="Edit">
                                            <x-ui-icon name="pencil" class="h-3.5 w-3.5" />
                                        </button>
                                        <form method="POST" action="{{ route('pertamina.ref.destroy', ['type' => 'status-proses', 'id' => $s->id]) }}"
                                              data-confirm-delete="Hapus status proses '{{ $s->nama }}'?" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-ink-soft hover:text-red-600 transition" title="Hapus">
                                                <x-ui-icon name="trash" class="h-3.5 w-3.5" />
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-3 py-4 text-center text-xs text-ink-soft">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- MODAL POP-UP DINAMIS --}}
        <div x-show="showModal"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/60 backdrop-blur-xs">

            <div @click.outside="showModal = false"
                 class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl border border-line p-5 sm:p-6">

                <div class="flex items-center justify-between border-b border-line pb-3 mb-4">
                    <h3 class="text-sm font-bold text-ink" x-text="modalTitle"></h3>
                    <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600">
                        <x-ui-icon name="x" class="h-4 w-4" />
                    </button>
                </div>

                <form :action="formAction" method="POST" class="space-y-4">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    {{-- Form khusus Tahap Pra-Kontrak --}}
                    <template x-if="type === 'tahap-pra-kontrak'">
                        <div class="space-y-3">
                            <div>
                                <label class="label">Nama Tahap Pra-Kontrak <span class="text-red-500">*</span></label>
                                <input type="text" name="tahap" x-model="form.tahap" required class="field" placeholder="Contoh: Penawaran Awal">
                            </div>
                            <div>
                                <label class="label">Bobot Progress (0.0 s/d 1.0) <span class="text-red-500">*</span></label>
                                <select name="bobot" x-model="form.bobot" required class="field font-mono font-semibold">
                                    <option value="0.0">0.0 (0%)</option>
                                    <option value="0.2">0.2 (20%)</option>
                                    <option value="0.4">0.4 (40%)</option>
                                    <option value="0.6">0.6 (60%)</option>
                                    <option value="0.8">0.8 (80%)</option>
                                    <option value="1.0">1.0 (100%)</option>
                                </select>
                            </div>
                        </div>
                    </template>

                    {{-- Form Hasil Penawaran atau Status Proses --}}
                    <template x-if="type === 'hasil-penawaran' || type === 'status-proses'">
                        <div>
                            <label class="label">
                                <span x-text="type === 'hasil-penawaran' ? 'Nama Hasil Penawaran' : 'Nama Status Proses'"></span>
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama" x-model="form.nama" required class="field" placeholder="Ketik nama...">
                        </div>
                    </template>

                    <div class="flex items-center justify-end gap-2 border-t border-line pt-3 mt-4">
                        <button type="button" @click="showModal = false" class="btn-ghost py-1.5 px-3 text-xs">Batal</button>
                        <button type="submit" class="btn-primary py-1.5 px-4 text-xs font-semibold shadow-sm">
                            <span x-text="isEdit ? 'Simpan' : 'Tambah'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
