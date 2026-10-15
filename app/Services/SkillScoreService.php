<?php
namespace App\Services;
use App\Models\Provider;
use App\Models\Badge;

class SkillScoreService {
    public function calculate(Provider $provider): float {
        $ratingScore = ($provider->average_rating / 5) * 40;
        $completionScore = ($provider->completion_rate / 100) * 30;
        $verificationScore = $provider->verification_status === 'verified' ? 20 : 0;
        $experienceScore = min($provider->experience_years / 10, 1) * 10;
        $score = $ratingScore + $completionScore + $verificationScore + $experienceScore;
        return round(min($score, 100), 2);
    }

    public function updateAndAssignBadges(Provider $provider): void {
        $provider->skill_score = $this->calculate($provider);
        $provider->save();
        $this->assignBadges($provider);
    }

    private function assignBadges(Provider $provider): void {
        $earnedBadgeIds = [];
        $badges = Badge::all()->keyBy('slug');

        if ($provider->average_rating >= 4.8 && isset($badges['top-rated'])) {
            $earnedBadgeIds[] = $badges['top-rated']->id;
        }
        if ($provider->completion_rate >= 95 && isset($badges['speedy-expert'])) {
            $earnedBadgeIds[] = $badges['speedy-expert']->id;
        }
        if ($provider->completed_jobs >= 100 && isset($badges['master-technician'])) {
            $earnedBadgeIds[] = $badges['master-technician']->id;
        }
        if ($provider->verification_status === 'verified' && isset($badges['verified-pro'])) {
            $earnedBadgeIds[] = $badges['verified-pro']->id;
        }
        if ($provider->skill_score >= 90 && isset($badges['neighborhood-favorite'])) {
            $earnedBadgeIds[] = $badges['neighborhood-favorite']->id;
        }

        $syncData = [];
        foreach ($earnedBadgeIds as $id) {
            $syncData[$id] = ['earned_at' => now()];
        }
        $provider->badges()->sync($syncData);
    }
}
