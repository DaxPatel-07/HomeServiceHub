<?php
namespace App\Http\Controllers\MasterAdmin;
use App\Http\Controllers\Controller;
use App\Models\Booking;

class ReportController extends Controller {
    public function index() {
        $monthExpr = config('database.default') === 'sqlite'
            ? "cast(strftime('%m', created_at) as integer)"
            : 'MONTH(created_at)';
        $yearExpr = config('database.default') === 'sqlite'
            ? "cast(strftime('%Y', created_at) as integer)"
            : 'YEAR(created_at)';
        $monthlyData = Booking::selectRaw("{$monthExpr} as month, {$yearExpr} as year, COUNT(*) as bookings, SUM(total_amount) as revenue")
            ->groupByRaw("{$yearExpr}, {$monthExpr}")
            ->orderByRaw('year DESC, month DESC')
            ->take(12)->get();
        $categoryStats = Booking::join('service_categories','bookings.category_id','=','service_categories.id')
            ->selectRaw('service_categories.name, COUNT(*) as total')
            ->groupBy('service_categories.name')
            ->orderByDesc('total')->get();
        return view('master-admin.reports', compact('monthlyData','categoryStats'));
    }
}
