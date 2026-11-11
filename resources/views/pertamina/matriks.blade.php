<x-app-layout title="Matriks Pra-Kontrak" subtitle="Rekap Project Pertamina Penawaran" active="pertamina.matriks">
    <div x-data="{
        showModal: false,
        isEdit: false,
        modalTitle: 'Tambah Proyek Baru',
        formAction: '{{ route('pertamina.projects.store') }}',
        formMethod: 'POST',
        form: {
            id: null,
            kode_project: '',
            nama_project: '',
            nilai_project: '',
            client: '',
            partner: 'FT UNDIP',
            pengelola: 'Semarang',
            progress_penawaran: 'Penawaran Awal',
            progress_pra_kontrak: '0.2',
            hasil_penawaran: 'Dalam Proses',
            status_pekerjaan: '',
            progress_pekerjaan: '',
            pic: ''
        },
        openCreate() {
            this.isEdit = false;
            this.modalTitle = '+ Add Proyek (Matriks Pra-Kontrak)';
            this.formAction = '{{ route('pertamina.projects.store') }}';
            this.formMethod = 'POST';
            this.form = {
                id: null,
                kode_project: '',
                nama_project: '',
                nilai_project: '',
                client: '',
                partner: 'FT UNDIP',
                pengelola: 'Semarang',
                progress_penawaran: 'Penawaran Awal',
                progress_pra_kontrak: '0.2',
                hasil_penawaran: 'Dalam Proses',
                status_pekerjaan: '',
                progress_pekerjaan: '',
                pic: ''
            };
            this.showModal = true;
        },
        openEdit(item) {
            this.isEdit = true;
            this.modalTitle = 'Edit Proyek: ' + item.kode_project;
            this.formAction = '/manajemen-proyek/pertamina/matriks/' + item.id;
            this.formMethod = 'PUT';
            this.form = {
                id: item.id,
                kode_project: item.kode_project || '',
                nama_project: item.nama_project || '',
                nilai_project: item.nilai_project ? Math.round(item.nilai_project) : '',
                client: item.client || '',
                partner: item.partner || '',
                pengelola: item.pengelola || 'Semarang',
                progress_penawaran: item.progress_penawaran || '',
                progress_pra_kontrak: item.progress_pra_kontrak !== null ? parseFloat(item.progress_pra_kontrak).toString() : '',
                hasil_penawaran: item.hasil_penawaran || 'Dalam Proses',
                status_pekerjaan: item.status_pekerjaan || '',
                progress_pekerjaan: item.progress_pekerjaan !== null ? parseFloat(item.progress_pekerjaan).toString() : '',
                pic: item.pic || ''
            };
            this.showModal = true;
        },
        formatRupiah(val) {
            if (!val) return '';
            let num = val.toString().replace(/[^0-9]/g, '');
            return 'Rp ' + (new Intl.NumberFormat('id-ID').format(num || 0));
        }
    }">

        {{-- Navigasi Tab Sub Bab 3 --}}
        @include('pertamina._tabs', ['active' => 'matriks'])

        {{-- 1. Ringkasan Header (Card di atas tabel) --}}
        <div class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-2 lg:grid-cols-5">
            {{-- Update Terakhir --}}
            <div class="card p-3.5 sm:p-4 border-l-4 border-l-forest">
                <p class="text-[0.68rem] font-semibold uppercase tracking-wider text-ink-soft">Update Terakhir</p>
                <p class="mt-1 text-sm font-bold text-ink sm:text-base">
                    {{ \Carbon\Carbon::parse($lastUpdate)->translatedFormat('d F Y') }}
                </p>
                <p class="text-[0.68rem] text-ink-soft">Data otomatis real-time</p>
            </div>

            {{-- Total Penawaran --}}
            <div class="card p-3.5 sm:p-4 border-l-4 border-l-[#2f5fbf]">
                <p class="text-[0.68rem] font-semibold uppercase tracking-wider text-ink-soft">Total Penawaran</p>
                <p class="mt-1 text-xl font-extrabold text-[#2f5fbf]">{{ $totalProjects }}</p>
                <p class="text-[0.68rem] text-ink-soft">Proyek Terdata</p>
            </div>

            {{-- Nilai Total Penawaran --}}
            <div class="card p-3.5 sm:p-4 border-l-4 border-l-leaf">
                <p class="text-[0.68rem] font-semibold uppercase tracking-wider text-ink-soft">Nilai Total Penawaran</p>
                <p class="mt-1 text-sm font-extrabold text-leaf sm:text-base">
                    Rp {{ number_format($totalNilaiPenawaran, 0, ',', '.') }}
                </p>
                <p class="text-[0.68rem] text-ink-soft">Seluruh usulan penawaran</p>
            </div>

            {{-- Total Penawaran Menang --}}
            <div class="card p-3.5 sm:p-4 border-l-4 border-l-forest">
                <p class="text-[0.68rem] font-semibold uppercase tracking-wider text-ink-soft">Penawaran Menang</p>
                <p class="mt-1 text-xl font-extrabold text-forest">{{ $totalMenang }}</p>
                <p class="text-[0.68rem] text-ink-soft">Proyek Deal / Berjalan</p>
            </div>

            {{-- Nilai Total Project Menang --}}
            <div class="card p-3.5 sm:p-4 border-l-4 border-l-olive col-span-2 sm:col-span-1">
                <p class="text-[0.68rem] font-semibold uppercase tracking-wider text-ink-soft">Nilai Project Menang</p>
                <p class="mt-1 text-sm font-extrabold text-[#6b6524] sm:text-base">
                    Rp {{ number_format($totalNilaiMenang, 0, ',', '.') }}
                </p>
                <p class="text-[0.68rem] text-ink-soft">Total proyek deal / berjalan</p>
            </div>
        </div>

        {{-- Flash Notification --}}
        @if (session('status'))
            <div class="mb-4 flex items-center justify-between rounded-xl bg-leaf-50 border border-leaf/30 px-4 py-3 text-xs font-semibold text-forest shadow-xs">
                <div class="flex items-center gap-2">
                    <x-ui-icon name="check" class="h-4 w-4 text-leaf" />
                    <span>{{ session('status') }}</span>
                </div>
            </div>
        @endif

        {{-- Filter & Action Bar --}}
        <div class="card mb-4 p-3 sm:p-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                {{-- Form Filter --}}
                <form method="GET" action="{{ route('pertamina.matriks') }}" class="flex flex-1 flex-wrap items-center gap-2">
                    <div class="relative flex-1 min-w-[12rem]">
                        <span class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-slate-400">
                            <x-ui-icon name="filter" class="h-3.5 w-3.5" />
                        </span>
                        <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Cari kode, nama proyek, client, PIC..." class="field pl-8 py-1.5 text-xs">
                    </div>

                    <select name="pengelola" class="field py-1.5 text-xs w-auto" onchange="this.form.submit()">
                        <option value="">Semua Pengelola</option>
                        <option value="Semarang" @selected($filters['pengelola'] === 'Semarang')>Semarang</option>
                        <option value="Bandung" @selected($filters['pengelola'] === 'Bandung')>Bandung</option>
                    </select>

                    <select name="hasil_penawaran" class="field py-1.5 text-xs w-auto" onchange="this.form.submit()">
                        <option value="">Semua Hasil</option>
                        @foreach ($refHasilPenawaran as $hp)
                            <option value="{{ $hp->nama }}" @selected($filters['hasil_penawaran'] === $hp->nama)>{{ $hp->nama }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn-forest py-1.5 px-3 text-xs">Filter</button>
                    @if ($filters['q'] || $filters['pengelola'] || $filters['hasil_penawaran'] || $filters['pic'])
                        <a href="{{ route('pertamina.matriks') }}" class="btn-ghost py-1.5 px-2.5 text-xs">Reset</a>
                    @endif
                </form>

                {{-- Tombol Aksi + Add Proyek --}}
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" @click="openCreate()" class="btn-primary py-2 px-3.5 text-xs inline-flex items-center gap-1.5 shadow-sm">
                        <x-ui-icon name="plus" class="h-4 w-4" />
                        <span>+ Add Proyek</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Tabel Matriks Pra-Kontrak --}}
        <div class="card overflow-hidden">
            <div class="table-wrap">
                <table class="w-full text-left text-[0.73rem] border-collapse">
                    <thead class="bg-[#f2f5f3] border-b border-line text-ink-soft uppercase text-[0.67rem] font-bold tracking-wider">
                        <tr>
                            <th class="px-2.5 py-2.5 text-center" style="width: 35px;">No.</th>
                            <th class="px-3 py-2.5">Kode Project</th>
                            <th class="px-3 py-2.5" style="min-width: 220px;">Nama Project</th>
                            <th class="px-3 py-2.5 text-right">Nilai Project (Rp)</th>
                            <th class="px-3 py-2.5">Client</th>
                            <th class="px-2.5 py-2.5">Partner</th>
                            <th class="px-2.5 py-2.5 text-center">Pengelola</th>
                            <th class="px-3 py-2.5">Progress Penawaran</th>
                            <th class="px-2.5 py-2.5 text-center">Pra-Kontrak (%)</th>
                            <th class="px-2.5 py-2.5 text-center">Hasil</th>
                            <th class="px-3 py-2.5">Status Pekerjaan</th>
                            <th class="px-2.5 py-2.5 text-center">Pekerjaan (%)</th>
                            <th class="px-2.5 py-2.5">PIC</th>
                            <th class="px-3 py-2.5 text-right" style="width: 80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line/70">
                        @forelse ($projects as $index => $item)
                            @php
                                $isMenang = strcasecmp($item->hasil_penawaran ?? '', 'Menang') === 0;
                                $isKalah = in_array(strtolower($item->hasil_penawaran ?? ''), ['kalah', 'batal', 'tidak lanjut']);
                            @endphp
                            <tr class="{{ $loop->even ? 'bg-[#f8fbf9]' : 'bg-white' }} hover:bg-[#eef6f0] transition-colors">
                                <td class="px-2.5 py-2 text-center text-ink-soft font-mono">
                                    {{ $projects->firstItem() + $index }}
                                </td>

                                <td class="px-3 py-2 font-mono font-bold text-forest whitespace-nowrap">
                                    {{ $item->kode_project }}
                                </td>

                                <td class="px-3 py-2 text-ink leading-snug font-medium">
                                    {{ $item->nama_project }}
                                </td>

                                <td class="px-3 py-2 text-right font-mono font-bold text-ink whitespace-nowrap">
                                    Rp {{ number_format($item->nilai_project, 0, ',', '.') }}
                                </td>

                                <td class="px-3 py-2 text-ink-soft text-[0.7rem] max-w-[140px] truncate" title="{{ $item->client }}">
                                    {{ $item->client }}
                                </td>

                                <td class="px-2.5 py-2 text-ink whitespace-nowrap">
                                    <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[0.67rem] font-semibold text-slate-700">
                                        {{ $item->partner ?: '-' }}
                                    </span>
                                </td>

                                <td class="px-2.5 py-2 text-center whitespace-nowrap">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-[0.67rem] font-semibold {{ $item->pengelola === 'Bandung' ? 'bg-sky-50 text-[#2f5fbf]' : 'bg-leaf-50 text-leaf' }}">
                                        {{ $item->pengelola }}
                                    </span>
                                </td>

                                <td class="px-3 py-2 text-ink font-medium whitespace-nowrap">
                                    {{ $item->progress_penawaran ?: '-' }}
                                </td>

                                <td class="px-2.5 py-2 text-center font-mono font-semibold whitespace-nowrap">
                                    @if ($item->progress_pra_kontrak !== null)
                                        <span class="rounded px-1.5 py-0.5 text-[0.68rem] {{ $item->progress_pra_kontrak >= 1.0 ? 'bg-emerald-100 text-emerald-800 font-bold' : ($item->progress_pra_kontrak >= 0.6 ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-700') }}">
                                            {{ round($item->progress_pra_kontrak * 100) }}%
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>

                                <td class="px-2.5 py-2 text-center whitespace-nowrap">
                                    @if ($isMenang)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[0.67rem] font-bold text-emerald-800">
                                            ● Menang
                                        </span>
                                    @elseif ($isKalah)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-[0.67rem] font-bold text-red-700">
                                            {{ $item->hasil_penawaran }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-[0.67rem] font-bold text-amber-800">
                                            {{ $item->hasil_penawaran ?: 'Dalam Proses' }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-3 py-2 text-ink-soft text-[0.7rem] max-w-[130px] truncate" title="{{ $item->status_pekerjaan }}">
                                    {{ $item->status_pekerjaan ?: '-' }}
                                </td>

                                <td class="px-2.5 py-2 text-center font-mono font-semibold whitespace-nowrap">
                                    @if ($item->progress_pekerjaan !== null)
                                        <span class="rounded px-1.5 py-0.5 text-[0.68rem] {{ $item->progress_pekerjaan >= 1.0 ? 'bg-emerald-100 text-emerald-800 font-bold' : 'bg-sky-50 text-[#2f5fbf]' }}">
                                            {{ round($item->progress_pekerjaan * 100) }}%
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>

                                <td class="px-2.5 py-2 text-ink font-semibold whitespace-nowrap">
                                    {{ $item->pic ?: '-' }}
                                </td>

                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1">
                                        <button type="button" @click="openEdit({{ json_encode($item) }})" class="rounded-lg p-1.5 text-ink-soft hover:bg-forest/10 hover:text-forest" title="Edit Proyek">
                                            <x-ui-icon name="pencil" class="h-3.5 w-3.5" />
                                        </button>
                                        <form method="POST" action="{{ route('pertamina.projects.destroy', $item) }}"
                                              data-confirm-title="Hapus Proyek?"
                                              data-confirm-delete="Apakah Anda yakin ingin menghapus proyek {{ $item->kode_project }} ({{ $item->nama_project }})? Data tidak dapat dipulihkan."
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg p-1.5 text-ink-soft hover:bg-red-50 hover:text-red-600 transition" title="Hapus Proyek">
                                                <x-ui-icon name="trash" class="h-3.5 w-3.5" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="px-4 py-8 text-center text-xs text-ink-soft">
                                    Tidak ada data proyek yang sesuai dengan kriteria pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($projects->hasPages())
                <div class="border-t border-line p-3">
                    {{ $projects->links() }}
                </div>
            @endif
        </div>

        {{-- ==================================================================== --}}
        {{-- MODAL POP-UP DINAMIS: + Add Proyek / Edit Proyek                      --}}
        {{-- ==================================================================== --}}
        <div x-show="showModal"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/60 backdrop-blur-xs overflow-y-auto">

            <div @click.outside="showModal = false"
                 class="relative w-full max-w-2xl max-h-[92vh] overflow-y-auto rounded-2xl bg-white shadow-2xl border border-line p-5 sm:p-6 my-auto">

                {{-- Header Modal --}}
                <div class="flex items-center justify-between border-b border-line pb-3 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="grid h-8 w-8 place-items-center rounded-xl bg-forest/10 text-forest">
                            <x-ui-icon name="box" class="h-4 w-4" />
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-ink" x-text="modalTitle"></h3>
                            <p class="text-[0.7rem] text-ink-soft">Isi rincian data proyek matriks penawaran Pertamina</p>
                        </div>
                    </div>
                    <button type="button" @click="showModal = false" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                        <x-ui-icon name="x" class="h-4 w-4" />
                    </button>
                </div>

                {{-- Form Modal --}}
                <form :action="formAction" method="POST" class="space-y-3.5">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="grid gap-3 sm:grid-cols-2">
                        {{-- 1. Kode Project --}}
                        <div>
                            <label class="label">1. Kode Project <span class="text-red-500">*</span></label>
                            <input type="text" name="kode_project" x-model="form.kode_project" required
                                   placeholder="Contoh: ENG-2026-030"
                                   class="field font-mono font-semibold">
                        </div>

                        {{-- 3. Nilai Project (Rp) --}}
                        <div>
                            <label class="label">3. Nilai Project (Rp) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" name="nilai_project" x-model="form.nilai_project" required min="0" step="1"
                                       placeholder="Contoh: 580000000"
                                       class="field font-mono font-bold text-forest">
                            </div>
                            <p class="mt-1 text-[0.68rem] text-leaf font-mono" x-text="formatRupiah(form.nilai_project)"></p>
                        </div>
                    </div>

                    {{-- 2. Nama Project --}}
                    <div>
                        <label class="label">2. Nama Project <span class="text-red-500">*</span></label>
                        <textarea name="nama_project" x-model="form.nama_project" required rows="2"
                                  placeholder="Ketikkan judul atau nama pekerjaan project..."
                                  class="field"></textarea>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-3">
                        {{-- 4. Client --}}
                        <div>
                            <label class="label">4. Client <span class="text-red-500">*</span></label>
                            <input type="text" name="client" x-model="form.client" required
                                   placeholder="Contoh: PT Pertamina Patra Niaga"
                                   class="field">
                        </div>

                        {{-- 5. Partner --}}
                        <div>
                            <label class="label">5. Partner</label>
                            <input type="text" list="partner-list" name="partner" x-model="form.partner"
                                   placeholder="Pilih atau ketik partner..."
                                   class="field">
                            <datalist id="partner-list">
                                @foreach ($partnerOptions as $opt)
                                    <option value="{{ $opt }}"></option>
                                @endforeach
                            </datalist>
                        </div>

                        {{-- 6. Pengelola --}}
                        <div>
                            <label class="label">6. Pengelola <span class="text-red-500">*</span></label>
                            <select name="pengelola" x-model="form.pengelola" required class="field">
                                @foreach ($pengelolaOptions as $p)
                                    <option value="{{ $p }}">{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        {{-- 7. Progress Penawaran (Referensi Pra-Kontrak) --}}
                        <div>
                            <label class="label">7. Progress Penawaran</label>
                            <select name="progress_penawaran" x-model="form.progress_penawaran" class="field">
                                <option value="">-- Pilih Progress Penawaran --</option>
                                @foreach ($refTahapPraKontrak as $tpk)
                                    <option value="{{ $tpk->tahap }}">{{ $tpk->tahap }} ({{ round($tpk->bobot * 100) }}%)</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 8. Progress Pra-Kontrak (%) - Manual dropdown pilihan user --}}
                        <div>
                            <label class="label">8. Progress Pra-Kontrak (%) <span class="text-xs text-ink-soft">(Manual)</span></label>
                            <select name="progress_pra_kontrak" x-model="form.progress_pra_kontrak" class="field font-mono font-semibold">
                                <option value="">-- Pilih Bobot / Persen --</option>
                                <option value="0.0">0% (0.0)</option>
                                <option value="0.2">20% (0.2)</option>
                                <option value="0.4">40% (0.4)</option>
                                <option value="0.6">60% (0.6)</option>
                                <option value="0.8">80% (0.8)</option>
                                <option value="1.0">100% (1.0)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-3">
                        {{-- 9. Hasil Penawaran --}}
                        <div>
                            <label class="label">9. Hasil Penawaran</label>
                            <select name="hasil_penawaran" x-model="form.hasil_penawaran" class="field font-semibold">
                                @foreach ($refHasilPenawaran as $hp)
                                    <option value="{{ $hp->nama }}">{{ $hp->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 10. Status Pekerjaan (Referensi Pelaksanaan) --}}
                        <div>
                            <label class="label">10. Status Pekerjaan</label>
                            <select name="status_pekerjaan" x-model="form.status_pekerjaan" class="field">
                                <option value="">- / Kosong</option>
                                @foreach ($refTahapPelaksanaan as $tpel)
                                    <option value="{{ $tpel->tahap }}">{{ $tpel->tahap }} ({{ round($tpel->bobot * 100) }}%)</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 11. Progress Pekerjaan (%) - Manual dropdown pilihan user --}}
                        <div>
                            <label class="label">11. Progress Pekerjaan (%) <span class="text-xs text-ink-soft">(Manual)</span></label>
                            <select name="progress_pekerjaan" x-model="form.progress_pekerjaan" class="field font-mono font-semibold">
                                <option value="">- / Kosong</option>
                                <option value="0.0">0% (0.0)</option>
                                <option value="0.2">20% (0.2)</option>
                                <option value="0.4">40% (0.4)</option>
                                <option value="0.6">60% (0.6)</option>
                                <option value="0.8">80% (0.8)</option>
                                <option value="1.0">100% (1.0)</option>
                            </select>
                        </div>
                    </div>

                    {{-- 12. PIC (Input Manual + Auto-Save ke Dropdown / Creatable Combobox) --}}
                    <div>
                        <label class="label">
                            12. PIC (Person In Charge)
                            <span class="text-[0.7rem] font-normal text-ink-soft">(Pilih dropdown ATAU ketik nama baru untuk otomatis disimpan ke master PIC)</span>
                        </label>
                        <div class="relative">
                            <input type="text" list="pic-options" name="pic" x-model="form.pic"
                                   placeholder="Ketik nama PIC baru atau pilih dari daftar..."
                                   class="field pr-10">
                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <x-ui-icon name="user" class="h-4 w-4" />
                            </span>
                            <datalist id="pic-options">
                                @foreach ($refPic as $picName)
                                    <option value="{{ $picName }}"></option>
                                @endforeach
                            </datalist>
                        </div>
                    </div>

                    {{-- Footer Action Buttons --}}
                    <div class="mt-5 flex items-center justify-end gap-2 border-t border-line pt-4">
                        <button type="button" @click="showModal = false" class="btn-ghost py-2 px-4 text-xs">
                            Batal
                        </button>
                        <button type="submit" class="btn-primary py-2 px-5 text-xs font-semibold shadow-sm">
                            <span x-text="isEdit ? 'Simpan Perubahan' : 'Tambah Proyek'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
