<?php

namespace App\Http\Controllers;


use App\Models\GuestCategory;
use Illuminate\Http\Request;

class CategoryService extends Controller
{

    public function index(Request $request)
    {
        $query = GuestCategory::query();
        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . $request->description . '%');
        }
        $query->whereNull('deleted_at');
        $query->orderBy('name');
        $categories = $query->get();
        return view('categories.index', compact('categories'));
    }



    public function create()
    {
        return view('categories.create');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:guest_categories,name',
            'description' => 'required|string|max:1000',
        ]);

        GuestCategory::create($validated);

        return redirect()->route('categories.index')->with('success', 'Category created successfully!');
    }


    public function show(string $id)
    {
        $category = GuestCategory::findOrFail($id);
        return view('categories.show', compact('category'));
    }


    public function edit(string $id)
    {
        $category = GuestCategory::findOrFail($id);
        return view('categories.edit', compact('category'));
    }


    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:guest_categories,name,' . $id,
            'description' => 'required|string|max:1000',
        ]);

        $category = GuestCategory::findOrFail($id);
        $category->update($validated);
        return redirect()->route('categories.index')->with('success', 'Category updated successfully!');
    }


    public function SoftDelete(string $id)
    {
        $category = GuestCategory::findOrFail($id);
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully!');
    }


    public function deleteAlert(Request $request, string $id)
    {
        $category = GuestCategory::findOrFail($id);
        return view('categories.deleteAlert', compact('category'));
    }


    public function handleOption(Request $request, string $id)
    {
        $option = $request->input('option');

        $category = GuestCategory::findOrFail($id);

        $allCategories = GuestCategory::get();

        session(['option' => $option]);

        return view('categories.option', compact('option', 'category', 'allCategories'));
    }


    public function UpdateThenDestroy(Request $request, string $id)
    {
        $validated = $request->validate([
            'new_category' => 'required|exists:guest_categories,id',
        ]);

        $category = GuestCategory::findOrFail($id);
        $newCategoryId = $validated['new_category'];

        $category->entries()->update(['guest_category_id' => $newCategoryId]);

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category updated and deleted successfully!');
    }
}
