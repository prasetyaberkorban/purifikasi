<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoanRequest;
use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\User;
use App\Services\LoanService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use RuntimeException;

class LoanController extends Controller
{
    public function __construct(private readonly LoanService $loans) {}

    public function active(Request $request): View
    {
        return view('loans.index', [
            'title' => 'Peminjaman Aktif',
            'subtitle' => 'Barang yang sedang berada di luar gudang.',
            'activeNav' => 'loans',
            'loans' => $this->query($request)->active()->paginate(12)->withQueryString(),
            'filters' => $this->filters($request),
            'mode' => 'active',
            'summary' => $this->summary(),
        ]);
    }

    public function history(Request $request): View
    {
        return view('loans.index', [
            'title' => 'Riwayat Peminjaman',
            'subtitle' => 'Seluruh transaksi pinjam dan kembali.',
            'activeNav' => 'loans',
            'loans' => $this->query($request)->paginate(12)->withQueryString(),
            'filters' => $this->filters($request),
            'mode' => 'history',
            'summary' => $this->summary(),
        ]);
    }

    public function create(Request $request): View
    {
        $assets = Asset::query()
            ->with(['category', 'location'])
            ->whereRaw('(quantity - borrowed_quantity) > 0')
            ->orderBy('name')
            ->get();

        $selected = $request->filled('asset') ? Asset::query()->find($request->integer('asset')) : null;

        return view('loans.create', [
            'assets' => $assets,
            'users' => User::query()->where('is_active', true)->orderBy('name')->get(),
            'selectedAsset' => $selected,
            'defaultDue' => now()->addDays((int) \App\Models\Setting::get('default_loan_days', '7'))->toDateString(),
        ]);
    }

    public function store(LoanRequest $request): RedirectResponse
    {
        $asset = Asset::query()->findOrFail($request->integer('asset_id'));
        $borrower = User::query()->findOrFail($request->integer('borrower_id'));

        $data = $request->validated();
        if ($request->hasFile('initial_photo')) {
            $data['initial_photo_path'] = $request->file('initial_photo')->store('loans', 'public');
        }

        try {
            $loan = $this->loans->borrow($asset, $borrower, $data, $request->user());
        } catch (RuntimeException $e) {
            return back()->withErrors(['quantity' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('loans.show', $loan)
            ->with('status', "Peminjaman {$loan->code} tercatat untuk {$borrower->name}.");
    }

    public function show(Request $request, AssetLoan $loan): View
    {
        $this->authorize('view', $loan);
        $loan->load(['asset.category', 'asset.location', 'borrower', 'handler']);

        return view('loans.show', [
            'loan' => $loan,
        ]);
    }

    public function returnForm(AssetLoan $loan): View
    {
        $this->authorize('return', $loan);
        $loan->load(['asset', 'borrower']);

        return view('loans.return', [
            'loan' => $loan,
        ]);
    }

    public function returnBack(Request $request, AssetLoan $loan): RedirectResponse
    {
        $this->authorize('return', $loan);

        $data = $request->validate([
            'condition_on_return' => ['required', 'in:baik,belum_dicek,rusak_ringan,rusak'],
            'return_photo'        => ['required', 'image', 'max:5120'],
            'note'                => ['nullable', 'string', 'max:500'],
        ]);

        $returnPhotoPath = $request->file('return_photo')->store('loan-returns', 'public');

        try {
            $this->loans->returnLoan($loan, $request->user(), $data['condition_on_return'], $data['note'] ?? null, $returnPhotoPath);
        } catch (RuntimeException $e) {
            return back()->withErrors(['loan' => $e->getMessage()]);
        }

        return redirect()
            ->route('loans.show', $loan)
            ->with('status', "Pengembalian {$loan->code} tercatat. Stok aset sudah diperbarui.");
    }

    public function destroy(Request $request, AssetLoan $loan): RedirectResponse
    {
        $this->authorize('delete', $loan);

        $code = $loan->code;
        $this->loans->deleteLoan($loan, $request->user());

        return back()->with('status', "Data peminjaman {$code} berhasil dihapus.");
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Hanya Admin yang dapat menghapus data peminjaman.');
        }

        $rawIds = $request->input('ids', []);
        if (is_string($rawIds)) {
            $rawIds = explode(',', $rawIds);
        }
        if ($request->filled('ids_csv')) {
            $rawIds = array_merge((array) $rawIds, explode(',', (string) $request->input('ids_csv')));
        }
        $ids = array_filter(array_unique(array_map('intval', (array) $rawIds)));

        if (empty($ids)) {
            return back()->withErrors(['ids' => 'Tidak ada data peminjaman yang dipilih.']);
        }

        $deletedCount = $this->loans->bulkDeleteLoans($ids, $request->user());

        return back()->with('status', "Berhasil menghapus {$deletedCount} data peminjaman.");
    }

    private function query(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $query = AssetLoan::query()
            ->with(['asset.category', 'borrower', 'handler'])
            ->search($this->filters($request)['q']);

        if (! $request->user()->isStaff()) {
            $query->where('borrower_id', $request->user()->getKey());
        }

        return $query;
    }

    /** @return array<string, string> */
    private function filters(Request $request): array
    {
        return [
            'q' => $request->string('q')->toString(),
            'from' => $request->string('from')->toString(),
            'to' => $request->string('to')->toString(),
        ];
    }

    /** @return array<string, int> */
    private function summary(): array
    {
        $base = AssetLoan::query();
        if (auth()->check() && ! auth()->user()->isStaff()) {
            $base->where('borrower_id', auth()->id());
        }

        return [
            'active' => (clone $base)->active()->count(),
            'units' => (int) (clone $base)->active()->sum('quantity'),
            'overdue' => (clone $base)->active()->whereDate('due_at', '<', today())->count(),
            'returned' => (clone $base)->where('status', 'dikembalikan')->count(),
        ];
    }
}