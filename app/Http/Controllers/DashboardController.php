<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetActivity;
use App\Services\InventoryStats;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(InventoryStats $stats): View
    {
        return view('dashboard', [
            'summary' => $stats->summary(),
            'conditions' => $stats->conditionBreakdown(),
            'categories' => $stats->categoryBreakdown(),
            'purchaseYears' => $stats->purchaseByYear(),
            'recentLoans' => $stats->recentLoans(4),
            'activities' => AssetActivity::query()->with('user')->latestFirst()->limit(5)->get(),
            'latestAssets' => Asset::query()->with(['category', 'location'])->latest()->limit(5)->get(),
        ]);
    }
}