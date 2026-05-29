<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'transaction_type' => 'required|in:income,expense',
            'kakeibo_type' => 'required|in:needs,wants,culture,unexpected,income',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_active'] = $request->boolean('is_active');

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function show(Category $category)
    {
        abort_unless($category->user_id === Auth::id(), 403);

        return view('categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        abort_unless($category->user_id === Auth::id(), 403);

        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        abort_unless($category->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'transaction_type' => 'required|in:income,expense',
            'kakeibo_type' => 'required|in:needs,wants,culture,unexpected,income',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        abort_unless($category->user_id === Auth::id(), 403);

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}