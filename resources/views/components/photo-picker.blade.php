@props([
    'name',
    'id' => null,
    'label' => null,
    'required' => false,
    'currentUrl' => null,
    'hint' => null,
])

@php
    $inputId = $id ?? $name;
@endphp

<div x-data="{
        preview: '{{ $currentUrl ?? '' }}',
        fileName: '',
        hasFile: {{ $currentUrl ? 'true' : 'false' }},
        sourceType: '',
        cameraModalOpen: false,
        cameraLoading: false,
        cameraError: '',
        stream: null,

        openCamera() {
            if (/Android|iPhone|iPad|Mobile/i.test(navigator.userAgent)) {
                this.$refs.cameraInput.click();
                return;
            }

            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                this.cameraModalOpen = true;
                this.cameraLoading = true;
                this.cameraError = '';
                navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                    .then(stream => {
                        this.stream = stream;
                        this.$nextTick(() => {
                            if (this.$refs.video) {
                                this.$refs.video.srcObject = stream;
                            }
                        });
                        this.cameraLoading = false;
                    })
                    .catch(err => {
                        this.cameraLoading = false;
                        this.cameraModalOpen = false;
                        this.$refs.cameraInput.click();
                    });
            } else {
                this.$refs.cameraInput.click();
            }
        },

        snapPhoto() {
            if (!this.$refs.video) return;
            const canvas = this.$refs.canvas;
            const video = this.$refs.video;
            canvas.width = video.videoWidth || 640;
            canvas.height = video.videoHeight || 480;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            canvas.toBlob(blob => {
                if (!blob) return;
                const file = new File([blob], 'foto-kamera-' + Date.now() + '.jpg', { type: 'image/jpeg' });
                this.setFile(file, 'kamera');
                this.closeCameraModal();
            }, 'image/jpeg', 0.9);
        },

        closeCameraModal() {
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }
            this.cameraModalOpen = false;
        },

        setFile(file, src) {
            if (!file) return;
            this.fileName = file.name;
            this.sourceType = src === 'kamera' ? 'Kamera' : 'Galeri';
            this.hasFile = true;
            const reader = new FileReader();
            reader.onload = (e) => {
                this.preview = e.target.result;
            };
            reader.readAsDataURL(file);

            const dt = new DataTransfer();
            dt.items.add(file);
            this.$refs.realInput.files = dt.files;
            this.$refs.realInput.dispatchEvent(new Event('change', { bubbles: true }));
        },

        clear() {
            this.preview = '';
            this.fileName = '';
            this.hasFile = false;
            this.sourceType = '';
            this.$refs.cameraInput.value = '';
            this.$refs.galleryInput.value = '';
            this.$refs.realInput.value = '';
            this.$refs.realInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }"
    class="space-y-2">

    @if ($label)
        <label class="label">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    {{-- Tombol Pilihan: Kamera atau Galeri --}}
    <div class="flex flex-wrap items-center gap-2">
        <button type="button" @click="openCamera()"
                class="inline-flex items-center gap-2 rounded-xl border border-line bg-canvas px-3.5 py-2 text-xs font-semibold text-ink hover:border-leaf hover:bg-leaf/10 hover:text-forest active:scale-95 transition">
            <x-ui-icon name="camera" class="h-4 w-4 text-leaf" />
            <span>Foto dari Kamera</span>
        </button>

        <button type="button" @click="$refs.galleryInput.click()"
                class="inline-flex items-center gap-2 rounded-xl border border-line bg-canvas px-3.5 py-2 text-xs font-semibold text-ink hover:border-forest hover:bg-forest/10 hover:text-forest active:scale-95 transition">
            <x-ui-icon name="photo" class="h-4 w-4 text-forest" />
            <span>Upload dari Galeri</span>
        </button>

        <template x-if="hasFile">
            <button type="button" @click="clear()"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-red-50/70 px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-100 active:scale-95 transition"
                    title="Hapus foto terpilih">
                <x-ui-icon name="trash" class="h-3.5 w-3.5" />
                <span>Hapus Foto</span>
            </button>
        </template>
    </div>

    {{-- Preview Foto --}}
    <template x-if="preview">
        <div class="relative mt-2.5 inline-block overflow-hidden rounded-xl border border-line bg-canvas p-1 shadow-sm">
            <img :src="preview" alt="Preview foto" class="max-h-48 max-w-full rounded-lg object-contain ring-1 ring-line">
            <div class="mt-1 flex items-center justify-between gap-2 px-1 text-[0.7rem] text-ink-soft">
                <span class="truncate max-w-[200px]" x-text="fileName || 'Foto terpilih'"></span>
                <span x-show="sourceType" class="rounded bg-forest/10 px-1.5 py-0.5 font-medium text-forest" x-text="sourceType"></span>
            </div>
        </div>
    </template>

    @if ($hint)
        <p class="text-[0.72rem] text-ink-soft">{{ $hint }}</p>
    @endif

    {{-- Real Input untuk dikirim ke Form --}}
    <input type="file" id="{{ $inputId }}" name="{{ $name }}" x-ref="realInput" class="sr-only"
           accept="image/*" @if($required) required @endif>

    {{-- Helper Inputs --}}
    <input type="file" x-ref="cameraInput" accept="image/*" capture="environment" class="sr-only"
           @change="setFile($event.target.files[0], 'kamera')">
    <input type="file" x-ref="galleryInput" accept="image/*" class="sr-only"
           @change="setFile($event.target.files[0], 'galeri')">

    {{-- Modal Kamera Langsung (Desktop / Laptop) --}}
    <template x-if="cameraModalOpen">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4 backdrop-blur-sm"
             @keydown.escape.window="closeCameraModal()">
            <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white p-5 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-line pb-3">
                    <div class="flex items-center gap-2">
                        <x-ui-icon name="camera" class="h-5 w-5 text-forest" />
                        <h3 class="text-sm font-bold text-ink">Ambil Foto dari Kamera</h3>
                    </div>
                    <button type="button" @click="closeCameraModal()" class="text-ink-soft hover:text-ink">
                        <x-ui-icon name="x" class="h-5 w-5" />
                    </button>
                </div>

                <div class="relative overflow-hidden rounded-xl bg-black aspect-video flex items-center justify-center">
                    <video x-ref="video" autoplay playsinline class="h-full w-full object-cover"></video>
                    <canvas x-ref="canvas" class="hidden"></canvas>
                    <div x-show="cameraLoading" class="absolute inset-0 flex items-center justify-center text-white text-xs">
                        Mengakses kamera...
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-line">
                    <button type="button" @click="closeCameraModal()" class="btn-ghost text-xs">Batal</button>
                    <button type="button" @click="snapPhoto()" class="btn-primary text-xs inline-flex items-center gap-1.5">
                        <x-ui-icon name="camera" class="h-4 w-4" />
                        <span>Jepret Foto</span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    @error($name)
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
