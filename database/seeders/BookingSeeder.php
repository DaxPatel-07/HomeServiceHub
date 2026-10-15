<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use App\Models\Provider;

class BookingSeeder extends Seeder {
    public function run(): void {
        $customers = User::where('role','customer')->get();
        $providers = Provider::where('verification_status','verified')->get();
        if ($customers->isEmpty() || $providers->isEmpty()) return;
        $statuses = ['completed','completed','completed','pending','accepted','cancelled'];
        $payMethods = ['cash','card','wallet'];
        for ($i = 0; $i < 30; $i++) {
            $customer = $customers->random();
            $provider = $providers->random();
            $status = $statuses[array_rand($statuses)];
            Booking::create([
                'booking_code' => Booking::generateCode(),
                'customer_id' => $customer->id,
                'provider_id' => $provider->id,
                'category_id' => $provider->category_id,
                'booking_date' => now()->subDays(rand(1,60))->format('Y-m-d'),
                'booking_time' => sprintf('%02d:00:00', rand(8,18)),
                'address' => '123 Demo Street, Block A',
                'city' => $customer->city ?? 'Mumbai',
                'description' => 'Sample service booking for demo purposes.',
                'service_charge' => $provider->hourly_rate,
                'total_amount' => $provider->hourly_rate + 50,
                'payment_method' => $payMethods[array_rand($payMethods)],
                'payment_status' => $status === 'completed' ? 'paid' : 'pending',
                'status' => $status,
            ]);
        }
    }
}
