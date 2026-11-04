<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(): View
    {
        return view('settings.locations', [
            'locations' => Location::query()->withCount('assets')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Location::query()->create($data);

        return back()->with('status', "Lokasi {$data['name']} ditambahkan.");
    }

    public function update(Request $request, Location $location): RedirectResponse
    {
        $location->update($this->validated($request, $location));

        return back()->with('status', "Lokasi {$location->name} diperbarui.");
    }

    public function destroy(Location $location): RedirectResponse
    {
        if ($location->assets()->exists()) {
            return back()->withErrors(['location' => "Lokasi {$location->name} masih dipakai oleh aset."]);
        }

        $name = $location->name;
        $location->delete();

        return back()->with('status', "Lokasi {$name} dihapus.");
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?Location $location = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:80'],
            'address' => ['nullable', 'string', 'max:255'],
            'pic_name' => ['nullable', 'string', 'max:120'],
            'pic_phone' => ['nullable', 'string', 'max:32'],
        ]);
    }
}