@props(['title' => 'Masuk'])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0D4C1E">
    <title>{{ $title }} · {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('img/purifikasi-logo.png') }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-dvh bg-canvas antialiased">
    <div class="grid min-h-dvh lg:grid-cols-2">
        {{-- Panel brand --}}
        <div class="relative hidden overflow-hidden bg-gradient-to-br from-forest via-forest to-forest-900 p-12 lg:flex lg:flex-col lg:items-center lg:justify-center">
            <div class="pointer-events-none absolute -top-24 -right-24 h-80 w-80 rounded-full bg-leaf/30 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-32 -left-20 h-96 w-96 rounded-full bg-olive/20 blur-3xl"></div>

            <div class="relative flex flex-col items-center text-center">
                <img src="{{ asset('img/purifikasi-logo.png') }}" alt="Purifikasi" class="h-32 w-32 rounded-3xl bg-white/95 p-4 object-contain shadow-lg">
                <p class="mt-6 text-2xl font-extrabold tracking-[0.14em] text-white">PURIFIKASI</p>
            </div>
        </div>

        {{-- Form --}}
        <div class="flex flex-col px-5 py-10 sm:px-8">
            <div class="flex flex-1 items-center justify-center">
                <div class="w-full max-w-sm">
                    <div class="mb-8 flex items-center gap-3 lg:hidden">
                        <img src="{{ asset('img/purifikasi-logo.png') }}" alt="" class="h-11 w-11 object-contain">
                        <p class="text-lg font-extrabold tracking-[0.12em] text-forest">PURIFIKASI</p>
                    </div>

                    <h1 class="text-2xl font-bold text-forest">{{ $title }}</h1>

                    @if (session('status'))
                        <div class="mt-4 rounded-xl border border-leaf/30 bg-leaf-50 px-4 py-3 text-sm font-medium text-forest">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3.5 text-sm text-red-700">
                            <div class="flex items-center gap-2 font-semibold">
                                <x-ui-icon name="warning" class="h-4 w-4 shrink-0 text-red-600" />
                                <span>Periksa kembali data Anda:</span>
                            </div>
                            <ul class="mt-1.5 list-inside list-disc text-xs space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mt-7">
                        {{ $slot }}
                    </div>
                </div>
            </div>

            <p class="mt-10 text-center text-[0.7rem] text-ink-soft">
                &copy; {{ now()->year }} PT. Purifikasi Andalan Diponegoro &middot; Office Database Kantor
            </p>
        </div>
    </div>
</body>
</html>
