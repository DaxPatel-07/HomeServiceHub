<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\ServiceCategory;
use Illuminate\Support\Str;

class ServiceCategorySeeder extends Seeder {
    public function run(): void {
        $categories = [
            ['name'=>'Electrician','icon'=>'fas fa-bolt','color'=>'#F59E0B','description'=>'Electrical repairs, wiring, circuit breakers, and installation services.'],
            ['name'=>'Plumber','icon'=>'fas fa-wrench','color'=>'#3B82F6','description'=>'Pipe repairs, leak fixing, water pressure, and drainage solutions.'],
            ['name'=>'Carpenter','icon'=>'fas fa-hammer','color'=>'#8B5CF6','description'=>'Furniture making, wood repairs, door and window fitting.'],
            ['name'=>'Painter','icon'=>'fas fa-paint-roller','color'=>'#EC4899','description'=>'Interior and exterior painting, wall finishing, and decorating.'],
            ['name'=>'AC Technician','icon'=>'fas fa-snowflake','color'=>'#06B6D4','description'=>'Air conditioner service, repair, installation, and gas refilling.'],
            ['name'=>'Cleaner','icon'=>'fas fa-broom','color'=>'#10B981','description'=>'Deep cleaning, regular maintenance, and post-construction cleanup.'],
            ['name'=>'Appliance Repair','icon'=>'fas fa-tools','color'=>'#F97316','description'=>'Repair of washing machines, refrigerators, microwaves, and more.'],
        ];
        foreach ($categories as $cat) {
            ServiceCategory::updateOrCreate(['slug' => Str::slug($cat['name'])], array_merge($cat, ['slug' => Str::slug($cat['name']), 'is_active' => true]));
        }
    }
}
