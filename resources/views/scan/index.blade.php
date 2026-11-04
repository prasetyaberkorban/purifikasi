<x-app-layout title="Scan QR / Barcode" subtitle="Arahkan kamera HP ke label aset untuk membuka detail barang" active="scan">
    <div class="grid gap-4 lg:grid-cols-3">
        <section class="card overflow-hidden lg:col-span-2"
                 x-data="qrScanner({
                     resolveUrl: '{{ route('scan.resolve') }}',
                     token: '{{ csrf_token() }}'
                 })">
            <div class="border-b border-line p-4 sm:p-5">
                <h2 class="text-sm font-bold text-ink">Kamera Scanner</h2>
                <p class="text-[0.72rem] text-ink-soft">Izinkan akses kamera saat diminta. Kamera hanya aktif setelah Anda menekan tombol.</p>
            </div>

            <div class="p-4 sm:p-5">
                <div x-show="!scanning" x-cloak="false" class="flex flex-col items-center gap-3 rounded-2xl border-2 border-dashed border-line bg-canvas py-12">
                    <span class="grid h-14 w-14 place-items-center rounded-2xl bg-leaf-50 text-leaf">
                        <x-ui-icon name="scan" class="h-7 w-7" />
                    </span>
                    <p class="text-sm text-ink-soft">Kamera belum aktif.</p>
                    <button type="button" class="btn-primary" @click="start()" :disabled="loading">
                        <span x-text="loading ? 'Menyiapkan kamera...' : 'Mulai Scan'">Mulai Scan</span>
                    </button>
                    <p class="text-[0.68rem] text-ink-soft" x-show="error" x-text="error"></p>
                </div>

                <div x-show="scanning" x-cloak class="space-y-3">
                    <div id="qr-reader" class="overflow-hidden rounded-2xl border border-line bg-black/90"></div>
                    <button type="button" class="btn-ghost w-full" @click="stop()">Hentikan Kamera</button>
                </div>
            </div>

            <form method="POST" action="{{ route('scan.resolve') }}" class="border-t border-line bg-canvas/60 p-4 sm:p-5">
                @csrf
                <label for="code" class="label">Input manual kode aset</label>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <input id="code" name="code" type="text" value="{{ old('code') }}" class="field sm:flex-1"
                           placeholder="Contoh: DEP.PL/2026/SGI.0007" autocomplete="off">
                    <button type="submit" class="btn-forest">Cari Aset</button>
                </div>
            </form>
        </section>

        <div class="space-y-4">
            <section class="card p-4 sm:p-5">
                <h2 class="mb-2 text-sm font-bold text-ink">Cara Pakai</h2>
                <ol class="space-y-2.5 text-[0.78rem] text-ink-soft">
                    <li class="flex gap-2.5"><span class="grid h-5 w-5 shrink-0 place-items-center rounded-full bg-leaf-50 text-[0.65rem] font-bold text-leaf">1</span> Tekan "Cetak Label QR" pada halaman aset, lalu tempel label di barang.</li>
                    <li class="flex gap-2.5"><span class="grid h-5 w-5 shrink-0 place-items-center rounded-full bg-leaf-50 text-[0.65rem] font-bold text-leaf">2</span> Buka halaman ini dari HP staf, tekan "Mulai Scan".</li>
                    <li class="flex gap-2.5"><span class="grid h-5 w-5 shrink-0 place-items-center rounded-full bg-leaf-50 text-[0.65rem] font-bold text-leaf">3</span> Detail aset terbuka otomatis; lanjutkan dengan tombol Pinjam.</li>
                </ol>
            </section>

            <section class="card p-4 sm:p-5">
                <h2 class="mb-2 text-sm font-bold text-ink">Label Siap Cetak</h2>
                <p class="text-[0.78rem] text-ink-soft">Cetak label QR atau barcode untuk banyak aset sekaligus.</p>
                @can('create', \App\Models\Asset::class)
                    <a href="{{ route('labels.index') }}" class="btn-olive mt-3 w-full">
                        <x-ui-icon name="qr" class="h-4 w-4" /> Buka Halaman Label
                    </a>
                @endcan
            </section>
        </div>
    </div>

    @push('scripts')
    @endpush
</x-app-layout>

<script type="module">
    document.addEventListener('alpine:init', () => {
        Alpine.data('qrScanner', ({ resolveUrl, token }) => ({
            scanner: null,
            scanning: false,
            loading: false,
            error: '',

            async start() {
                this.error = '';
                this.loading = true;

                try {
                    const { Html5Qrcode } = await import('html5-qrcode');

                    this.scanner = this.scanner ?? new Html5Qrcode('qr-reader', { verbose: false });
                    await this.scanner.start(
                        { facingMode: 'environment' },
                        { fps: 10, qrbox: { width: 240, height: 240 } },
                        (decodedText) => this.onScan(decodedText, resolveUrl, token),
                        () => {},
                    );

                    this.scanning = true;
                } catch (e) {
                    this.error = 'Kamera tidak dapat diakses: ' + (e?.message ?? e);
                } finally {
                    this.loading = false;
                }
            },

            async stop() {
                try {
                    if (this.scanner) {
                        await this.scanner.stop();
                        await this.scanner.clear();
                    }
                } catch (e) {
                    // kamera sudah berhenti
                }

                this.scanning = false;
            },

            onScan(text, url, csrf) {
                this.stop();
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.innerHTML = `<input type="hidden" name="_token" value="${csrf}"><input type="hidden" name="code" value="${text.replace(/"/g, '&quot;')}">`;
                document.body.appendChild(form);
                form.submit();
            },
        }));
    });
</script>