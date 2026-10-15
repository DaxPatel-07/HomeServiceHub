<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Badge;
use App\Models\Provider;
use App\Models\User;

class BadgeSeeder extends Seeder {
    public function run(): void {
        $badges = [
            ['name'=>'Top Rated','slug'=>'top-rated','description'=>'Awarded for maintaining 4.8+ star rating','icon'=>'fas fa-star','color'=>'#F59E0B'],
            ['name'=>'Speedy Expert','slug'=>'speedy-expert','description'=>'95%+ completion rate','icon'=>'fas fa-bolt','color'=>'#3B82F6'],
            ['name'=>'Master Technician','slug'=>'master-technician','description'=>'Completed 100+ jobs','icon'=>'fas fa-medal','color'=>'#8B5CF6'],
            ['name'=>'Verified Pro','slug'=>'verified-pro','description'=>'Background verified professional','icon'=>'fas fa-shield-alt','color'=>'#10B981'],
            ['name'=>'Neighborhood Favorite','slug'=>'neighborhood-favorite','description'=>'Skill score 90+ in local area','icon'=>'fas fa-heart','color'=>'#EC4899'],
        ];
        foreach ($badges as $b) {
            Badge::updateOrCreate(['slug'=>$b['slug']], $b);
        }
        // Assign badges to verified providers
        $verifiedProviders = Provider::where('verification_status','verified')->get();
        $verifiedBadge = Badge::where('slug','verified-pro')->first();
        $topRated = Badge::where('slug','top-rated')->first();
        $master = Badge::where('slug','master-technician')->first();
        $speedy = Badge::where('slug','speedy-expert')->first();
        $neighborhood = Badge::where('slug','neighborhood-favorite')->first();
        foreach ($verifiedProviders as $p) {
            $toSync = [];
            if ($verifiedBadge) $toSync[$verifiedBadge->id] = ['earned_at' => now()];
            if ($p->average_rating >= 4.8 && $topRated) $toSync[$topRated->id] = ['earned_at' => now()];
            if ($p->completed_jobs >= 100 && $master) $toSync[$master->id] = ['earned_at' => now()];
            if ($p->completion_rate >= 95 && $speedy) $toSync[$speedy->id] = ['earned_at' => now()];
            if ($p->skill_score >= 90 && $neighborhood) $toSync[$neighborhood->id] = ['earned_at' => now()];
            $p->badges()->sync($toSync);
        }
    }
}
