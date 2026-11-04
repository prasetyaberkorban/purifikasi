<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use App\Services\LabelService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ScanController extends Controller
{
    public function __construct(private readonly LabelService $labels) {}

    public function index(): View
    {
        return view('scan.index');
    }

    /** Cari aset dari hasil scan (kode aset atau URL label). */
    public function resolve(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:255'],
        ]);

        $needle = trim($data['code']);

        if (Str::startsWith($needle, ['http://', 'https://'])) {
            $path = parse_url($needle, PHP_URL_PATH) ?: '';
            $needle = trim(rawurldecode(Str::after($path, '/aset/kode/')), '/');
        }

        $asset = Asset::query()
            ->where('code', $needle)
            ->orWhere('serial_number', $needle)
            ->first();

        if (! $asset) {
            return back()->withErrors(['code' => "Aset dengan kode \"{$needle}\" tidak ditemukan."])->withInput();
        }

        return redirect()->route('assets.show', $asset)->with('status', "Aset {$asset->name} ditemukan.");
    }

    /** Endpoint QR: /aset/{code} */
    public function show(string $code, Request $request): View|RedirectResponse
    {
        $asset = Asset::query()->where('code', $code)->first();

        if (! $asset) {
            return redirect()->route('scan.index')->withErrors(['code' => "Kode aset {$code} tidak terdaftar."]);
        }

        return redirect()->route('assets.show', $asset);
    }

    public function labels(Request $request): View
    {
        $filters = [
            'q' => $request->string('q')->toString(),
            'category' => $request->string('category')->toString(),
            'ids' => collect($request->input('ids', []))->filter()->map(fn ($id) => (int) $id)->all(),
        ];

        $assets = Asset::query()
            ->with(['category', 'location'])
            ->search($filters['q'])
            ->when($filters['category'] !== '', fn ($q) => $q->where('category_id', $filters['category']))
            ->when($filters['ids'] !== [], fn ($q) => $q->whereIn('id', $filters['ids']))
            ->orderBy('code')
            ->limit(60)
            ->get();

        return view('labels.index', [
            'assets' => $assets,
            'filters' => $filters,
            'categories' => Category::query()->orderBy('name')->get(),
            'labels' => $this->labels,
        ]);
    }

    public function print(Request $request): View
    {
        $ids = collect($request->input('ids', []))->map(fn ($id) => (int) $id)->filter()->all();
        $mode = $request->string('mode', 'qr')->toString();

        $assets = Asset::query()
            ->with(['category', 'location'])
            ->when($ids !== [], fn ($q) => $q->whereIn('id', $ids), fn ($q) => $q->orderBy('code')->limit(30))
            ->orderBy('code')
            ->get();

        return view('labels.print', [
            'assets' => $assets,
            'mode' => $mode,
            'labels' => $this->labels,
        ]);
    }
}