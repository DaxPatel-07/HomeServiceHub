<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Provider;
use App\Models\Booking;
use App\Models\Complaint;
use App\Models\Review;

class DashboardController extends Controller {
    public function index() {
        $stats = [
            'users' => User::where('role','customer')->count(),
            'providers' => Provider::count(),
            'bookings' => Booking::count(),
            'revenue' => Booking::where('status','completed')->sum('total_amount'),
            'pending_providers' => Provider::where('verification_status','pending')->count(),
            'open_complaints' => Complaint::where('status','open')->count(),
            'reviews' => Review::count(),
        ];
        $pendingProviders = Provider::with('user','category')->where('verification_status','pending')->latest()->take(5)->get();
        $recentBookings = Booking::with(['customer','provider.user','category'])->latest()->take(5)->get();
        $openComplaints = Complaint::with(['customer','booking'])->where('status','open')->latest()->take(5)->get();
        $bookingsByStatus = Booking::selectRaw('status, COUNT(*) as count')->groupBy('status')->pluck('count','status');
        return view('admin.dashboard', compact('stats','pendingProviders','recentBookings','openComplaints','bookingsByStatus'));
    }
}
