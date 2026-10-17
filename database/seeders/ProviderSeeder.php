<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Provider;
use App\Models\User;
use App\Models\ServiceCategory;
use App\Services\SkillScoreService;

class ProviderSeeder extends Seeder {
    public function run(): void {
        $cats = ServiceCategory::all()->keyBy('slug');
        $data = [
            ['email'=>'cool@provider.com','cat'=>'ac-technician','biz'=>'Cool Experts Ahmedabad','bio'=>'5-star AC specialists with 8 years of experience. We service all major AC brands across Ahmedabad & Gandhinagar.','exp'=>8,'area'=>'Ahmedabad, SG Highway, Bopal, Satellite','rate'=>599,'rating'=>4.9,'total'=>145,'completed'=>138,'status'=>'verified','lat'=>23.0304,'lng'=>72.5178,'locality'=>'Satellite','building'=>'Shivalik Heights','postal'=>'380015'],
            ['email'=>'plumb@provider.com','cat'=>'plumber','biz'=>'Quick Plumbers Gujarat','bio'=>'Fast and reliable plumbing solutions. Available 24/7 for emergency repairs in Ahmedabad.','exp'=>6,'area'=>'Ahmedabad, Navrangpura, Vastrapur, Ashram Road','rate'=>399,'rating'=>4.8,'total'=>120,'completed'=>112,'status'=>'verified','lat'=>23.0365,'lng'=>72.5611,'locality'=>'Navrangpura','building'=>'Nilamber Bunglows','postal'=>'380009'],
            ['email'=>'wood@provider.com','cat'=>'carpenter','biz'=>'Wood Masters Gandhinagar','bio'=>'Custom furniture and woodwork experts. Quality craftsmanship guaranteed in Gandhinagar sectors.','exp'=>10,'area'=>'Gandhinagar, Sector 1-30, Infocity, Kudasan','rate'=>499,'rating'=>4.8,'total'=>95,'completed'=>90,'status'=>'verified','lat'=>23.1932,'lng'=>72.6300,'locality'=>'Infocity','building'=>'Infocity Green','postal'=>'382007'],
            ['email'=>'paint@provider.com','cat'=>'painter','biz'=>'Paint Pro Ahmedabad','bio'=>'Professional painting with premium Asian Paints. Interior and exterior specialists.','exp'=>5,'area'=>'Ahmedabad, Maninagar, Chandkheda, Motera','rate'=>349,'rating'=>4.7,'total'=>80,'completed'=>75,'status'=>'verified','lat'=>22.9978,'lng'=>72.6033,'locality'=>'Maninagar','building'=>'Asian Paints Hub','postal'=>'380008'],
            ['email'=>'ac@provider.com','cat'=>'ac-technician','biz'=>'CoolAir Tech Gandhinagar','bio'=>'Certified AC technicians with expertise in all brands and models across Gandhinagar.','exp'=>7,'area'=>'Gandhinagar, GIFT City, Sargasan, Randesan','rate'=>549,'rating'=>4.8,'total'=>110,'completed'=>100,'status'=>'verified','lat'=>23.1610,'lng'=>72.6840,'locality'=>'GIFT City','building'=>'GIFT One','postal'=>'382355'],
            ['email'=>'spark@provider.com','cat'=>'electrician','biz'=>'SparkElectric Ahmedabad','bio'=>'Licensed electricians for all your electrical needs. Trusted by 200+ local homes.','exp'=>9,'area'=>'Ahmedabad, Prahlad Nagar, Bodakdev, Thaltej','rate'=>449,'rating'=>4.9,'total'=>160,'completed'=>155,'status'=>'verified','lat'=>23.0373,'lng'=>72.5120,'locality'=>'Bodakdev','building'=>'Judges Bunglow Complex','postal'=>'380054'],
            ['email'=>'clean@provider.com','cat'=>'cleaner','biz'=>'CleanHome Gandhinagar','bio'=>'Deep cleaning specialists using eco-friendly products across Gandhinagar and North Ahmedabad.','exp'=>4,'area'=>'Gandhinagar, Infocity, Sector 21, Ahmedabad Motera','rate'=>299,'rating'=>4.7,'total'=>200,'completed'=>195,'status'=>'verified','lat'=>23.2324,'lng'=>72.6479,'locality'=>'Sector 21','building'=>'Sector 21 Complex','postal'=>'382021'],
            ['email'=>'fixit@provider.com','cat'=>'appliance-repair','biz'=>'Fix-It Appliances Ahmedabad','bio'=>'Expert appliance repair for all brands. Same-day doorstep service.','exp'=>6,'area'=>'Ahmedabad, Gota, Chandlodia, Naranpura','rate'=>399,'rating'=>4.6,'total'=>130,'completed'=>122,'status'=>'verified','lat'=>23.0977,'lng'=>72.5453,'locality'=>'Gota','building'=>'Vandematram Arcade','postal'=>'382481'],
        ];
        $scoreService = new SkillScoreService();
        foreach ($data as $d) {
            $user = User::where('email',$d['email'])->first();
            if (!$user) continue;
            $cat = $cats[$d['cat']] ?? $cats->first();
            $provider = Provider::updateOrCreate(['user_id'=>$user->id],[
                'category_id'=>$cat->id,'business_name'=>$d['biz'],'bio'=>$d['bio'],
                'experience_years'=>$d['exp'],'service_area'=>$d['area'],'hourly_rate'=>$d['rate'],
                'average_rating'=>$d['rating'],'total_jobs'=>$d['total'],'completed_jobs'=>$d['completed'],
                'verification_status'=>$d['status'],'is_available'=>true,'is_active'=>true,
                'latitude'=>$d['lat'],'longitude'=>$d['lng'],'locality'=>$d['locality'],
                'building'=>$d['building'],'postal_code'=>$d['postal'],'service_radius_km'=>5.00,
            ]);
            $provider->skill_score = $scoreService->calculate($provider);
            $provider->save();
        }
    }
}
