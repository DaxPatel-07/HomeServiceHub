<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller {
    public function index() {
        $categories = ServiceCategory::withCount('providers')->latest()->get();
        return view('admin.categories', compact('categories'));
    }
    public function store(Request $request) {
        $request->validate(['name'=>'required|string|max:100','description'=>'nullable|string','icon'=>'nullable|string','color'=>'nullable|string']);
        ServiceCategory::create(array_merge($request->only('name','description','icon','color'), ['slug' => Str::slug($request->name)]));
        return back()->with('success', 'Category created!');
    }
}
