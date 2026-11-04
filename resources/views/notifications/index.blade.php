<x-app-layout title="Notifikasi" subtitle="Pemeriksaan dan verifikasi kondisi aset" active="notifications">
    <div class="max-w-4xl">
        <section class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-4 py-4 sm:px-5">
                <div>
                    <h2 class="text-sm font-bold text-ink">Belum Pernah Dicek</h2>
                    <p class="text-[0.72rem] text-ink-soft">Aset yang kondisinya belum diverifikasi.</p>
                </div>
                <span class="badge-muted">{{ $unchecked->count() }}</span>
            </div>

            <ul class="divide-y divide-line">
                @forelse ($unchecked as $asset)
                    <li class="flex items-center justify-between gap-3 px-4 py-3.5 sm:px-5">
                        <div class="min-w-0">
                            <a href="{{ route('assets.show', $asset) }}" class="text-sm font-semibold text-ink hover:text-forest">{{ $asset->name }}</a>
                            <p class="font-mono text-[0.68rem] text-ink-soft">{{ $asset->code }}</p>
                            @php
                                $rooms = $asset->items->pluck('room')->filter()->unique();
                                $roomDisplay = $rooms->isNotEmpty() ? $rooms->implode(', ') : ($asset->room ?? '-');
                            @endphp
                            <p class="text-[0.72rem] text-ink-soft">{{ $asset->category?->name }} &middot; Ruangan: {{ $roomDisplay }}</p>
                        </div>
                        @can('update', $asset)
                            <form method="POST" action="{{ route('assets.check', $asset) }}">
                                @csrf
                                <button type="submit" class="btn-soft px-3 py-1.5 text-xs">Tandai Dicek</button>
                            </form>
                        @endcan
                    </li>
                @empty
                    <li class="px-4 py-10 text-center text-sm text-ink-soft">Semua aset sudah pernah dicek.</li>
                @endforelse
            </ul>
        </section>
    </div>
</x-app-layout>
