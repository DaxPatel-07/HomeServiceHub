<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller {
    public function index() {
        $complaints = Complaint::with(['customer', 'booking'])->latest()->paginate(15);
        return view('admin.complaints', compact('complaints'));
    }
    public function resolve(Complaint $complaint, Request $request) {
        $request->validate(['resolution' => 'required|string|min:5']);
        $complaint->update(['status' => 'resolved', 'resolution' => $request->resolution]);
        return back()->with('success', 'Complaint resolved.');
    }
}
