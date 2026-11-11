<x-app-layout title="Dashboard Pra-Kontrak" subtitle="Rekap Project Pertamina Penawaran" active="pertamina.dashboard">
    {{-- Navigasi Tab Sub Bab 3 --}}
    @include('pertamina._tabs', ['active' => 'dashboard'])

    {{-- KPI Cards --}}
    <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Total Project --}}
        <div class="card p-4 border-l-4 border-l-forest">
            <p class="text-[0.68rem] font-semibold uppercase tracking-wider text-ink-soft">Total Project</p>
            <p class="mt-1 text-2xl font-extrabold text-forest">{{ $totalProjects }}</p>
            <p class="text-[0.68rem] text-ink-soft">Proyek penawaran tercatat</p>
        </div>

        {{-- Total Nilai Penawaran --}}
        <div class="card p-4 border-l-4 border-l-leaf">
            <p class="text-[0.68rem] font-semibold uppercase tracking-wider text-ink-soft">Nilai Total Penawaran</p>
            <p class="mt-1 text-lg font-extrabold text-leaf sm:text-xl">
                Rp {{ number_format($totalNilaiPenawaran, 0, ',', '.') }}
            </p>
            <p class="text-[0.68rem] text-ink-soft">Akumulasi usulan penawaran</p>
        </div>

        {{-- Total Nilai Penawaran Deal/Menang --}}
        <div class="card p-4 border-l-4 border-l-[#2f5fbf]">
            <p class="text-[0.68rem] font-semibold uppercase tracking-wider text-ink-soft">Nilai Deal / Menang</p>
            <p class="mt-1 text-lg font-extrabold text-[#2f5fbf] sm:text-xl">
                Rp {{ number_format($totalNilaiMenang, 0, ',', '.') }}
            </p>
            <p class="text-[0.68rem] text-ink-soft">Penawaran menang &amp; berjalan</p>
        </div>

        {{-- Rata-rata Progress --}}
        <div class="card p-4 border-l-4 border-l-olive">
            <p class="text-[0.68rem] font-semibold uppercase tracking-wider text-ink-soft">Rata-rata Progress</p>
            <p class="mt-1 text-2xl font-extrabold text-[#6b6524]">
                {{ number_format($avgProgress, 1, ',', '.') }}%
            </p>
            <p class="text-[0.68rem] text-ink-soft">Bobot progress pra-kontrak</p>
        </div>
    </div>

    {{-- Grid Tabel Rekapitulasi Otomatis --}}
    <div class="grid gap-5 lg:grid-cols-2">

        {{-- Tabel Ringkasan 1: Hasil Penawaran --}}
        <div class="card overflow-hidden">
            <div class="border-b border-line bg-[#f2f5f3] px-4 py-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-forest flex items-center gap-2">
                    <x-ui-icon name="clipboard-check" class="h-4 w-4" />
                    <span>Ringkasan 1: Status Hasil Penawaran</span>
                </h3>
            </div>
            <div class="table-wrap">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-canvas border-b border-line text-ink-soft text-[0.68rem] font-semibold uppercase">
                        <tr>
                            <th class="px-4 py-2.5">Hasil Penawaran</th>
                            <th class="px-3 py-2.5 text-center">Jumlah Proyek</th>
                            <th class="px-4 py-2.5 text-right">Nilai Proyek (Rp)</th>
                            <th class="px-3 py-2.5 text-center">Porsi (%)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line/60">
                        @foreach ($hasilGroups as $item)
                            <tr class="hover:bg-leaf-50/40">
                                <td class="px-4 py-2.5 font-semibold text-ink">
                                    {{ $item['name'] }}
                                </td>
                                <td class="px-3 py-2.5 text-center font-bold text-forest">
                                    {{ $item['count'] }}
                                </td>
                                <td class="px-4 py-2.5 text-right font-mono font-medium text-ink">
                                    Rp {{ number_format($item['total_nilai'], 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-2.5 text-center font-mono text-[0.7rem] text-ink-soft">
                                    {{ number_format($item['percentage'], 1, ',', '.') }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tabel Ringkasan 2: Pengelola --}}
        <div class="card overflow-hidden">
            <div class="border-b border-line bg-[#f2f5f3] px-4 py-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-forest flex items-center gap-2">
                    <x-ui-icon name="box" class="h-4 w-4" />
                    <span>Ringkasan 2: Rekapitulasi Pengelola</span>
                </h3>
            </div>
            <div class="table-wrap">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-canvas border-b border-line text-ink-soft text-[0.68rem] font-semibold uppercase">
                        <tr>
                            <th class="px-4 py-2.5">Pengelola / Kantor</th>
                            <th class="px-3 py-2.5 text-center">Jumlah Proyek</th>
                            <th class="px-4 py-2.5 text-right">Total Nilai Proyek (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line/60">
                        @foreach ($pengelolaGroups as $item)
                            <tr class="hover:bg-leaf-50/40">
                                <td class="px-4 py-2.5 font-semibold text-ink">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold {{ $item['name'] === 'Bandung' ? 'bg-sky-50 text-[#2f5fbf]' : 'bg-leaf-50 text-leaf' }}">
                                        {{ $item['name'] }}
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 text-center font-bold text-forest">
                                    {{ $item['count'] }}
                                </td>
                                <td class="px-4 py-2.5 text-right font-mono font-medium text-ink">
                                    Rp {{ number_format($item['total_nilai'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tabel Ringkasan 3: Tahap Proses Pra-Kontrak --}}
        <div class="card overflow-hidden">
            <div class="border-b border-line bg-[#f2f5f3] px-4 py-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-forest flex items-center gap-2">
                    <x-ui-icon name="chart" class="h-4 w-4" />
                    <span>Ringkasan 3: Tahap Proses Pra-Kontrak</span>
                </h3>
            </div>
            <div class="table-wrap">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-canvas border-b border-line text-ink-soft text-[0.68rem] font-semibold uppercase">
                        <tr>
                            <th class="px-4 py-2.5">Tahap Progress Penawaran</th>
                            <th class="px-3 py-2.5 text-center">Jumlah Proyek</th>
                            <th class="px-4 py-2.5 text-right">Total Nilai (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line/60">
                        @foreach ($tahapGroups as $item)
                            <tr class="hover:bg-leaf-50/40">
                                <td class="px-4 py-2.5 font-semibold text-ink">
                                    {{ $item['name'] }}
                                </td>
                                <td class="px-3 py-2.5 text-center font-bold text-forest">
                                    {{ $item['count'] }}
                                </td>
                                <td class="px-4 py-2.5 text-right font-mono font-medium text-ink">
                                    Rp {{ number_format($item['total_nilai'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tabel Ringkasan 4: PIC yang Menangani Proyek --}}
        <div class="card overflow-hidden">
            <div class="border-b border-line bg-[#f2f5f3] px-4 py-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-forest flex items-center gap-2">
                    <x-ui-icon name="user" class="h-4 w-4" />
                    <span>Ringkasan 4: PIC &amp; Jumlah Project Ditangani</span>
                </h3>
            </div>
            <div class="table-wrap">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-canvas border-b border-line text-ink-soft text-[0.68rem] font-semibold uppercase">
                        <tr>
                            <th class="px-4 py-2.5">Nama PIC</th>
                            <th class="px-3 py-2.5 text-center">Jumlah Proyek</th>
                            <th class="px-4 py-2.5 text-right">Total Nilai Ditangani (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line/60">
                        @forelse ($picGroups as $item)
                            <tr class="hover:bg-leaf-50/40">
                                <td class="px-4 py-2.5 font-semibold text-ink">
                                    {{ $item['name'] }}
                                </td>
                                <td class="px-3 py-2.5 text-center font-bold text-forest">
                                    {{ $item['count'] }}
                                </td>
                                <td class="px-4 py-2.5 text-right font-mono font-medium text-ink">
                                    Rp {{ number_format($item['total_nilai'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-xs text-ink-soft">
                                    Belum ada data penugasan PIC pada proyek.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
