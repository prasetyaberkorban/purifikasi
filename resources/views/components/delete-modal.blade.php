<div x-data="deleteConfirmModal()"
     x-cloak
     @open-delete-modal.window="open($event.detail)"
     @keydown.escape.window="close()"
     class="relative z-50">
    {{-- Backdrop --}}
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-forest-900/40 backdrop-blur-xs transition-opacity"
         @click="close()"
         aria-hidden="true"></div>

    {{-- Modal Dialog --}}
    <div x-show="isOpen"
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div x-show="isOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-3 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-3 sm:translate-y-0 sm:scale-95"
             class="relative w-full max-w-[22rem] rounded-3xl bg-white p-6 sm:p-7 text-center shadow-2xl ring-1 ring-black/5"
             @click.stop
             role="dialog"
             aria-modal="true">

            {{-- Ilustrasi Tempat Sampah Hijau dengan Sparkles --}}
            <div class="relative mx-auto flex h-24 w-24 items-center justify-center mb-3">
                {{-- Ellipse bayangan bawah hijau lembut --}}
                <div class="absolute bottom-1 h-3 w-16 rounded-full bg-leaf-100/90"></div>

                {{-- Bintang/sparkles (+) dan titik di sekeliling --}}
                <span class="absolute top-1 left-3 text-xs font-bold text-leaf select-none leading-none animate-pulse">+</span>
                <span class="absolute top-3 right-3 text-sm font-bold text-leaf select-none leading-none">+</span>
                <span class="absolute bottom-4 left-2 text-sm font-bold text-leaf select-none leading-none">+</span>
                <span class="absolute top-2 right-8 h-1.5 w-1.5 rounded-full bg-leaf select-none"></span>
                <span class="absolute bottom-3 right-4 h-1 w-1 rounded-full bg-leaf select-none"></span>

                {{-- Ikon Tempat Sampah (Gaya Cute Tilted Lid) --}}
                <svg class="relative h-14 w-14 text-forest drop-shadow-sm" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    {{-- Tutup agak miring --}}
                    <g transform="rotate(-8 32 18)">
                        {{-- Pegangan tutup --}}
                        <rect x="25" y="8" width="14" height="4" rx="2" fill="currentColor"/>
                        {{-- Plat tutup --}}
                        <rect x="13" y="13" width="38" height="5" rx="2.5" fill="currentColor"/>
                    </g>
                    {{-- Badan tong sampah --}}
                    <path d="M18 21H46L43.2 52.8C43.0 54.6 41.5 56 39.7 56H24.3C22.5 56 21.0 54.6 20.8 52.8L18 21Z" fill="currentColor"/>
                    {{-- 3 Garis putih di tengah badan tong --}}
                    <rect x="24.5" y="26" width="3" height="21" rx="1.5" fill="white"/>
                    <rect x="30.5" y="26" width="3" height="21" rx="1.5" fill="white"/>
                    <rect x="36.5" y="26" width="3" height="21" rx="1.5" fill="white"/>
                </svg>
            </div>

            {{-- Judul Konfirmasi --}}
            <h3 class="text-base sm:text-lg font-bold text-ink" x-text="title">Hapus Data?</h3>

            {{-- Deskripsi / Pesan --}}
            <p class="mt-2 text-xs leading-relaxed text-ink-soft" x-text="message">
                Data akan dihapus secara permanen dan tidak dapat dipulihkan kembali.
            </p>

            {{-- Tombol Batal & Hapus (Sesuai Desain: Batal Outline, Hapus Solid Hijau) --}}
            <div class="mt-6 flex items-center gap-3">
                <button type="button"
                        @click="close()"
                        class="flex-1 rounded-xl border border-line bg-white py-2.5 px-3 text-xs font-semibold text-ink hover:bg-canvas active:scale-95 transition">
                    <span x-text="cancelText">Batal</span>
                </button>
                <button type="button"
                        @click="confirmAction()"
                        class="flex-1 rounded-xl bg-forest hover:bg-forest-700 py-2.5 px-3 text-xs font-semibold text-white shadow-sm hover:shadow active:scale-95 transition">
                    <span x-text="confirmText">Hapus</span>
                </button>
            </div>
        </div>
    </div>
</div>
