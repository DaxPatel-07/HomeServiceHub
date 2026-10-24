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
        $request->validate(['name'=>'required|string|max:100|unique:service_categories,name','description'=>'nullable|string|max:1000','icon'=>'nullable|string|max:50','color'=>'nullable|string|max:20']);
        ServiceCategory::create(array_merge($request->only('name','description','icon','color'), ['slug' => Str::slug($request->name)]));
        return back()->with('success', 'Category created!');
    }
}
