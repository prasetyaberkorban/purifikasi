<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('settings.categories', [
            'categories' => Category::query()->withCount('assets')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:24', 'unique:categories,code'],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'color' => ['required', 'string', 'max:16'],
        ]);

        Category::query()->create($data);

        return back()->with('status', "Kategori {$data['name']} ditambahkan.");
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:24', 'unique:categories,code,'.$category->id],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'color' => ['required', 'string', 'max:16'],
        ]);

        $category->update($data);

        return back()->with('status', "Kategori {$category->name} diperbarui.");
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->assets()->exists()) {
            return back()->withErrors(['category' => "Kategori {$category->name} masih dipakai oleh aset."]);
        }

        $name = $category->name;
        $category->delete();

        return back()->with('status', "Kategori {$name} dihapus.");
    }
}