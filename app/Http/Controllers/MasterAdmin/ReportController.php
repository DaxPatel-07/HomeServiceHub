<?php
namespace App\Http\Controllers\MasterAdmin;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Provider;

class ReportController extends Controller {
    public function index() {
        $monthlyData = Booking::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as bookings, SUM(total_amount) as revenue')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('year DESC, month DESC')
            ->take(12)->get();
        $categoryStats = Booking::join('service_categories','bookings.category_id','=','service_categories.id')
            ->selectRaw('service_categories.name, COUNT(*) as total')
            ->groupBy('service_categories.name')
            ->orderByDesc('total')->get();
        return view('master-admin.reports', compact('monthlyData','categoryStats'));
    }
}
