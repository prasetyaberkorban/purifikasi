<x-app-layout title="Petunjuk Pra-Kontrak" subtitle="Rekap Project Pertamina Penawaran" active="pertamina.petunjuk">
    <div x-data="{
        showModal: false,
        isEdit: false,
        type: 'panduan',
        modalTitle: '',
        formAction: '',
        form: {
            id: null,
            bagian_warna: '',
            fungsi: '',
            cara_penggunaan: '',
            tahap: '',
            bobot: '0.10'
        },
        openAdd(type) {
            this.isEdit = false;
            this.type = type;
            this.formAction = '/manajemen-proyek/pertamina/referensi/' + type;
            this.form = { id: null, bagian_warna: '', fungsi: '', cara_penggunaan: '', tahap: '', bobot: '0.10' };

            if (type === 'panduan') {
                this.modalTitle = '+ Add Panduan Penggunaan';
            } else {
                this.modalTitle = '+ Add Standar Bobot Tahap Proses';
            }
            this.showModal = true;
        },
        openEdit(type, item) {
            this.isEdit = true;
            this.type = type;
            this.formAction = '/manajemen-proyek/pertamina/referensi/' + type + '/' + item.id;
            this.form = {
                id: item.id,
                bagian_warna: item.bagian_warna || '',
                fungsi: item.fungsi || '',
                cara_penggunaan: item.cara_penggunaan || '',
                tahap: item.tahap || '',
                bobot: item.bobot !== undefined ? parseFloat(item.bobot).toString() : '0.10'
            };

            if (type === 'panduan') {
                this.modalTitle = 'Edit Panduan Penggunaan';
            } else {
                this.modalTitle = 'Edit Standar Bobot: ' + item.tahap;
            }
            this.showModal = true;
        }
    }">

        {{-- Navigasi Tab Sub Bab 3 --}}
        @include('pertamina._tabs', ['active' => 'petunjuk'])

        {{-- Flash Notification --}}
        @if (session('status'))
            <div class="mb-4 flex items-center justify-between rounded-xl bg-leaf-50 border border-leaf/30 px-4 py-3 text-xs font-semibold text-forest shadow-xs">
                <div class="flex items-center gap-2">
                    <x-ui-icon name="check" class="h-4 w-4 text-leaf" />
                    <span>{{ session('status') }}</span>
                </div>
            </div>
        @endif

        <div class="space-y-6">

            {{-- Tabel 1: Panduan Penggunaan --}}
            <div class="card overflow-hidden">
                <div class="flex items-center justify-between border-b border-line bg-[#f2f5f3] px-4 py-3.5">
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-forest">1. Panduan Penggunaan Sistem</h3>
                        <p class="text-[0.68rem] text-ink-soft">Petunjuk kerja, arti kode warna, dan tata cara pengisian</p>
                    </div>
                    <button type="button" @click="openAdd('panduan')" class="btn-primary py-1.5 px-3 text-xs inline-flex items-center gap-1 shadow-xs">
                        <x-ui-icon name="plus" class="h-3.5 w-3.5" />
                        <span>+ Add Petunjuk</span>
                    </button>
                </div>
                <div class="table-wrap">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-canvas border-b border-line text-ink-soft text-[0.67rem] font-semibold uppercase">
                            <tr>
                                <th class="px-3 py-2.5 text-center" style="width: 35px;">No</th>
                                <th class="px-4 py-2.5" style="width: 140px;">Bagian / Warna</th>
                                <th class="px-4 py-2.5" style="width: 220px;">Fungsi</th>
                                <th class="px-4 py-2.5">Cara Penggunaan</th>
                                <th class="px-3 py-2.5 text-right" style="width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line/60">
                            @forelse ($panduanList as $idx => $p)
                                <tr class="hover:bg-leaf-50/40">
                                    <td class="px-3 py-2.5 text-center text-ink-soft font-mono">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-2.5 font-bold text-forest whitespace-nowrap">
                                        <span class="rounded bg-forest/10 px-2 py-0.5 text-xs text-forest">
                                            {{ $p->bagian_warna }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 font-semibold text-ink">
                                        {{ $p->fungsi }}
                                    </td>
                                    <td class="px-4 py-2.5 text-ink-soft leading-relaxed text-[0.73rem]">
                                        {{ $p->cara_penggunaan }}
                                    </td>
                                    <td class="px-3 py-2.5 text-right whitespace-nowrap">
                                        <button type="button" @click="openEdit('panduan', {{ json_encode($p) }})" class="p-1 text-ink-soft hover:text-forest" title="Edit">
                                            <x-ui-icon name="pencil" class="h-3.5 w-3.5" />
                                        </button>
                                        <form method="POST" action="{{ route('pertamina.ref.destroy', ['type' => 'panduan', 'id' => $p->id]) }}"
                                              data-confirm-delete="Hapus panduan '{{ $p->bagian_warna }}'?" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-ink-soft hover:text-red-600 transition" title="Hapus">
                                                <x-ui-icon name="trash" class="h-3.5 w-3.5" />
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-6 text-center text-xs text-ink-soft">Belum ada panduan penggunaan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tabel 2: Standar Bobot Tahap Proses --}}
            <div class="card overflow-hidden">
                <div class="flex items-center justify-between border-b border-line bg-[#f2f5f3] px-4 py-3.5">
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-forest">2. Standar Bobot Tahap Proses</h3>
                        <p class="text-[0.68rem] text-ink-soft">Daftar tahapan penawaran &amp; standar bobot pembobotan persentase</p>
                    </div>
                    <button type="button" @click="openAdd('bobot-proses')" class="btn-primary py-1.5 px-3 text-xs inline-flex items-center gap-1 shadow-xs">
                        <x-ui-icon name="plus" class="h-3.5 w-3.5" />
                        <span>+ Add Bobot Tahap</span>
                    </button>
                </div>
                <div class="table-wrap">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-canvas border-b border-line text-ink-soft text-[0.67rem] font-semibold uppercase">
                            <tr>
                                <th class="px-3 py-2.5 text-center" style="width: 35px;">No</th>
                                <th class="px-4 py-2.5">Tahap Proses</th>
                                <th class="px-3 py-2.5 text-center" style="width: 140px;">Bobot</th>
                                <th class="px-3 py-2.5 text-center" style="width: 120px;">Persentase</th>
                                <th class="px-3 py-2.5 text-right" style="width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line/60">
                            @forelse ($bobotList as $idx => $b)
                                <tr class="hover:bg-leaf-50/40">
                                    <td class="px-3 py-2.5 text-center text-ink-soft font-mono">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-2.5 font-medium text-ink">{{ $b->tahap }}</td>
                                    <td class="px-3 py-2.5 text-center font-mono font-bold text-forest">
                                        {{ number_format($b->bobot, 2) }}
                                    </td>
                                    <td class="px-3 py-2.5 text-center font-mono font-bold">
                                        <span class="rounded px-2 py-0.5 text-xs {{ $b->bobot >= 1.0 ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                            {{ round($b->bobot * 100) }}%
                                        </span>
                                    </td>
                                    <td class="px-3 py-2.5 text-right whitespace-nowrap">
                                        <button type="button" @click="openEdit('bobot-proses', {{ json_encode($b) }})" class="p-1 text-ink-soft hover:text-forest" title="Edit">
                                            <x-ui-icon name="pencil" class="h-3.5 w-3.5" />
                                        </button>
                                        <form method="POST" action="{{ route('pertamina.ref.destroy', ['type' => 'bobot-proses', 'id' => $b->id]) }}"
                                              data-confirm-delete="Hapus bobot tahap '{{ $b->tahap }}'?" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-ink-soft hover:text-red-600 transition" title="Hapus">
                                                <x-ui-icon name="trash" class="h-3.5 w-3.5" />
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-6 text-center text-xs text-ink-soft">Belum ada standar bobot proses.</td></tr>
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
                 class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl border border-line p-5 sm:p-6">

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

                    {{-- Form Panduan Penggunaan --}}
                    <template x-if="type === 'panduan'">
                        <div class="space-y-3">
                            <div>
                                <label class="label">Bagian / Warna <span class="text-red-500">*</span></label>
                                <input type="text" name="bagian_warna" x-model="form.bagian_warna" required class="field" placeholder="Contoh: Hijau / Biru / Deadline">
                            </div>
                            <div>
                                <label class="label">Fungsi <span class="text-red-500">*</span></label>
                                <input type="text" name="fungsi" x-model="form.fungsi" required class="field" placeholder="Ketik ringkasan fungsi...">
                            </div>
                            <div>
                                <label class="label">Cara Penggunaan <span class="text-red-500">*</span></label>
                                <textarea name="cara_penggunaan" x-model="form.cara_penggunaan" required rows="3" class="field" placeholder="Instruksi dan petunjuk penggunaan..."></textarea>
                            </div>
                        </div>
                    </template>

                    {{-- Form Standar Bobot Tahap Proses --}}
                    <template x-if="type === 'bobot-proses'">
                        <div class="space-y-3">
                            <div>
                                <label class="label">Tahap Proses <span class="text-red-500">*</span></label>
                                <input type="text" name="tahap" x-model="form.tahap" required class="field" placeholder="Contoh: Telaah TOR / RKS">
                            </div>
                            <div>
                                <label class="label">Bobot Desimal (0.00 s/d 1.00) <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" min="0" max="1" name="bobot" x-model="form.bobot" required class="field font-mono font-bold" placeholder="0.25">
                            </div>
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
