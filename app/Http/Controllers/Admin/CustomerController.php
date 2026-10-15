<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;

class CustomerController extends Controller {
    public function index() {
        $customers = User::where('role','customer')->latest()->paginate(15);
        return view('admin.customers', compact('customers'));
    }
}
