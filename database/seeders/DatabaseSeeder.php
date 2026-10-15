<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $this->call([
            ServiceCategorySeeder::class,
            UserSeeder::class,
            ProviderSeeder::class,
            BadgeSeeder::class,
            BookingSeeder::class,
            ReviewSeeder::class,
            MaintenanceReminderSeeder::class,
        ]);
    }
}
