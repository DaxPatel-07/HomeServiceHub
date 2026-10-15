<?php
namespace App\Http\Controllers\MasterAdmin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Provider;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Complaint;

class DashboardController extends Controller {
    public function index() {
        $systemStats = [
            'total_users' => User::count(),
            'customers' => User::where('role','customer')->count(),
            'providers' => Provider::count(),
            'verified_providers' => Provider::where('verification_status','verified')->count(),
            'admins' => User::where('role','admin')->count(),
            'total_bookings' => Booking::count(),
            'completed_bookings' => Booking::where('status','completed')->count(),
            'total_revenue' => Booking::where('status','completed')->sum('total_amount'),
            'open_complaints' => Complaint::where('status','open')->count(),
            'avg_rating' => round(Review::avg('rating'), 2),
        ];
        $monthlyBookings = Booking::selectRaw('MONTH(created_at) as month, COUNT(*) as total, SUM(total_amount) as revenue')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')->orderBy('month')->get();
        $recentUsers = User::latest()->take(5)->get();
        $topProviders = Provider::with('user')->orderByDesc('skill_score')->take(5)->get();
        $health = [
            'uptime' => '98%', 'database' => 'Excellent', 'api_usage' => '62%',
            'security' => '74%', 'last_backup' => now()->subHours(3)->diffForHumans(),
            'server_load' => '24%', 'security_alerts' => 3, 'active_users' => User::where('is_active',true)->count(),
        ];
        return view('master-admin.dashboard', compact('systemStats','monthlyBookings','recentUsers','topProviders','health'));
    }
}
