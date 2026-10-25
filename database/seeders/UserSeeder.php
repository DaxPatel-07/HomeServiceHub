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
