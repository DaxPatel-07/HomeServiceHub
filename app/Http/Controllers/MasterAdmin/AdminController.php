<?php
namespace App\Http\Controllers\MasterAdmin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller {
    public function index() {
        $admins = User::where('role','admin')->latest()->paginate(15);
        return view('master-admin.admins', compact('admins'));
    }
    public function store(Request $request) {
        $request->validate(['name'=>'required','email'=>'required|email|unique:users','password'=>'required|min:8','phone'=>'required']);
        User::create(array_merge($request->only('name','email','phone'), ['password'=>Hash::make($request->password),'role'=>'admin']));
        return back()->with('success', 'Admin created!');
    }
    public function destroy(User $user) {
        abort_if($user->role !== 'admin', 403, 'Can only delete admin accounts.');
        abort_if($user->id === auth()->id(), 403, 'You cannot delete your own account.');
        $user->delete();
        return back()->with('success', 'Admin removed.');
    }
}
