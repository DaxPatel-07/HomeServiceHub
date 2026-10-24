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
        $resolution = $request->input('resolution') ?: $request->input('resolution_notes');
        $request->merge(['resolution' => $resolution]);
        $request->validate(['resolution' => 'required|string|min:5|max:2000']);
        $complaint->update(['status' => 'resolved', 'resolution' => $resolution]);
        return back()->with('success', 'Complaint resolved.');
    }
}
