<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run(): void {
        // Master Admin
        User::updateOrCreate(['email'=>'masteradmin@hsh.com'],['name'=>'Master Admin','password'=>Hash::make('password'),'role'=>'master_admin','phone'=>'9000000001','city'=>'Ahmedabad','is_active'=>true]);
        // Admin
        User::updateOrCreate(['email'=>'admin@hsh.com'],['name'=>'Admin User','password'=>Hash::make('password'),'role'=>'admin','phone'=>'9000000002','city'=>'Gandhinagar','is_active'=>true]);
        // Customers
        $customers = [
            ['name'=>'John Doe','email'=>'john@example.com','city'=>'Ahmedabad','phone'=>'9111111111','address'=>'Flat 402, Shivalik Heights, SG Highway, Ahmedabad'],
            ['name'=>'Priya Sharma','email'=>'priya@example.com','city'=>'Gandhinagar','phone'=>'9222222222','address'=>'Plot 12, Sector 21, Gandhinagar'],
            ['name'=>'Rahul Verma','email'=>'rahul@example.com','city'=>'Ahmedabad','phone'=>'9333333333','address'=>'B-103, Iscon Platinum, Bopal, Ahmedabad'],
            ['name'=>'Ananya Singh','email'=>'ananya@example.com','city'=>'Gandhinagar','phone'=>'9444444444','address'=>'House 55, Infocity Green, Kudasan, Gandhinagar'],
            ['name'=>'Vikram Patel','email'=>'vikram@example.com','city'=>'Ahmedabad','phone'=>'9555555555','address'=>'12, Nilamber Bunglows, Navrangpura, Ahmedabad'],
        ];
        foreach ($customers as $c) {
            User::updateOrCreate(['email'=>$c['email']], array_merge($c, ['password'=>Hash::make('password'),'role'=>'customer','is_active'=>true]));
        }
        // Providers (users)
        $providers = [
            ['name'=>'Cool Experts','email'=>'cool@provider.com','city'=>'Ahmedabad','phone'=>'9600000001'],
            ['name'=>'Quick Plumbers','email'=>'plumb@provider.com','city'=>'Ahmedabad','phone'=>'9600000002'],
            ['name'=>'Wood Masters','email'=>'wood@provider.com','city'=>'Gandhinagar','phone'=>'9600000003'],
            ['name'=>'Paint Pro','email'=>'paint@provider.com','city'=>'Ahmedabad','phone'=>'9600000004'],
            ['name'=>'CoolAir Tech','email'=>'ac@provider.com','city'=>'Gandhinagar','phone'=>'9600000005'],
            ['name'=>'SparkElectric','email'=>'spark@provider.com','city'=>'Ahmedabad','phone'=>'9600000006'],
            ['name'=>'CleanHome','email'=>'clean@provider.com','city'=>'Gandhinagar','phone'=>'9600000007'],
            ['name'=>'Fix-It Appliances','email'=>'fixit@provider.com','city'=>'Ahmedabad','phone'=>'9600000008'],
        ];
        foreach ($providers as $p) {
            User::updateOrCreate(['email'=>$p['email']], array_merge($p, ['password'=>Hash::make('password'),'role'=>'provider','is_active'=>true]));
        }
    }
}
