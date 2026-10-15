<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\MaintenanceReminder;
use App\Models\User;
use App\Models\ServiceCategory;

class MaintenanceReminderSeeder extends Seeder {
    public function run(): void {
        $customer = User::where('role','customer')->first();
        if (!$customer) return;
        $ac = ServiceCategory::where('slug','ac-technician')->first();
        $clean = ServiceCategory::where('slug','cleaner')->first();
        if ($ac) {
            MaintenanceReminder::updateOrCreate(
                ['customer_id'=>$customer->id,'category_id'=>$ac->id],
                ['title'=>'AC Service Due','notes'=>'Annual AC servicing and gas refilling','next_due_date'=>now()->addDays(5)->format('Y-m-d'),'repeat_months'=>6,'is_active'=>true]
            );
        }
        if ($clean) {
            MaintenanceReminder::updateOrCreate(
                ['customer_id'=>$customer->id,'category_id'=>$clean->id],
                ['title'=>'Deep Cleaning Session','notes'=>'Monthly home deep cleaning','next_due_date'=>now()->addDays(12)->format('Y-m-d'),'repeat_months'=>1,'is_active'=>true]
            );
        }
    }
}
