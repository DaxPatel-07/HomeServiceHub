<?php
namespace App\Http\Controllers;
use App\Models\ServiceCategory;
use App\Models\Provider;
use App\Models\Booking;
use App\Models\User;

class HomeController extends Controller {
    public function index() {
        $categories = ServiceCategory::where('is_active', true)->get();
        $topProviders = Provider::with(['user', 'category', 'badges'])
            ->where('verification_status', 'verified')
            ->where('is_active', true)
            ->orderByDesc('skill_score')
            ->take(6)->get();

        $neighborhoodProviders = Provider::with(['user', 'category', 'badges'])
            ->where('verification_status', 'verified')
            ->where('is_active', true)
            ->neighborhoodTrusted()
            ->orderByDesc('skill_score')
            ->take(4)->get();

        $stats = [
            'customers' => User::where('role', 'customer')->count(),
            'providers' => Provider::where('verification_status', 'verified')->count(),
            'bookings' => Booking::where('status', 'completed')->count(),
            'rating' => '4.8',
        ];
        return view('home.index', compact('categories', 'topProviders', 'neighborhoodProviders', 'stats'));
    }
}
