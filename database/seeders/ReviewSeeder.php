<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Review;

class ReviewSeeder extends Seeder {
    public function run(): void {
        $completedBookings = Booking::where('status','completed')->get();
        $comments = [
            'Excellent service! Very professional and punctual.',
            'Great work, highly recommend this provider.',
            'Fixed the issue quickly and efficiently.',
            'Very satisfied with the service quality.',
            'Professional, clean, and thorough work.',
            'Good service, will book again.',
            'Quick response and excellent results.',
        ];
        foreach ($completedBookings as $booking) {
            if (!Review::where('booking_id',$booking->id)->exists()) {
                $rating = rand(4,5);
                Review::create([
                    'booking_id' => $booking->id,
                    'customer_id' => $booking->customer_id,
                    'provider_id' => $booking->provider_id,
                    'rating' => $rating,
                    'comment' => $comments[array_rand($comments)],
                    'is_visible' => true,
                ]);
            }
        }
        // Update provider ratings
        $providers = \App\Models\Provider::all();
        foreach ($providers as $p) {
            $avg = Review::where('provider_id',$p->id)->avg('rating');
            if ($avg) $p->update(['average_rating' => round($avg,2)]);
        }
    }
}
