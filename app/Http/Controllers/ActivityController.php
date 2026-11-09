<?php

namespace App\Http\Controllers;

use App\Models\AssetActivity;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->toString();
        $type = $request->string('type')->toString();
        $date = $request->string('date')->toString();

        $query = AssetActivity::query()
            ->with(['user', 'asset'])
            ->latestFirst();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('asset', fn ($aq) => $aq->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"));
            });
        }

        if ($type !== '') {
            $query->where('type', $type);
        }

        if ($date !== '') {
            $query->whereDate('created_at', $date);
        }

        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        $todayCount = (clone $query)->whereDate('created_at', today())->count();
        $totalCount = (clone $query)->count();

        $activities = $query->paginate(20)->withQueryString();

        $types = [
            'created' => 'Aset Dibuat',
            'updated' => 'Aset Diperbarui',
            'loan' => 'Peminjaman',
            'return' => 'Pengembalian',
            'condition' => 'Pemeriksaan / Service',
            'deleted' => 'Aset Dihapus',
            'delete_loan' => 'Peminjaman Dihapus',
        ];

        return view('activities.index', [
            'activities' => $activities,
            'search' => $search,
            'selectedType' => $type,
            'selectedDate' => $date,
            'todayCount' => $todayCount,
            'totalCount' => $totalCount,
            'types' => $types,
        ]);
    }

    public function destroy(Request $request, AssetActivity $activity): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Hanya Admin yang dapat menghapus data log aktivitas.');
        }

        $desc = $activity->description;
        $activity->delete();

        return redirect()->route('activities.index')->with('status', "Log aktivitas '{$desc}' berhasil dihapus.");
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Hanya Admin yang dapat menghapus data log aktivitas.');
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
            return back()->withErrors(['ids' => 'Tidak ada log aktivitas yang dipilih.']);
        }

        $count = AssetActivity::whereIn('id', $ids)->delete();

        return back()->with('status', "Berhasil menghapus {$count} log aktivitas terpilih.");
    }
}
