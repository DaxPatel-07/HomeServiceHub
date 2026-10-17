<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Provider extends Model {
    use HasFactory;
    protected $fillable = ['user_id','category_id','business_name','bio','experience_years','service_area','hourly_rate','skill_score','total_jobs','completed_jobs','average_rating','verification_status','is_available','is_active','id_proof','certificate','latitude','longitude','locality','building','postal_code','service_radius_km'];
    protected $casts = ['is_available' => 'boolean', 'is_active' => 'boolean', 'skill_score' => 'float', 'average_rating' => 'float', 'latitude' => 'float', 'longitude' => 'float', 'service_radius_km' => 'float'];

    public function user() { return $this->belongsTo(User::class); }
    public function category() { return $this->belongsTo(ServiceCategory::class, 'category_id'); }
    public function bookings() { return $this->hasMany(Booking::class, 'provider_id'); }
    public function reviews() { return $this->hasMany(Review::class, 'provider_id'); }
    public function badges() { return $this->belongsToMany(Badge::class, 'provider_badges')->withPivot('earned_at')->withTimestamps(); }
    public function splitItems() { return $this->hasMany(SplitBookingItem::class, 'provider_id'); }

    public function getNameAttribute() { return $this->business_name ?: $this->user->name; }
    public function getCompletionRateAttribute() {
        if ($this->total_jobs == 0) return 0;
        return round(($this->completed_jobs / $this->total_jobs) * 100, 1);
    }

    public function isNeighborhoodTrusted(): bool {
        return $this->badges->contains('slug', 'neighborhood-favorite') || ($this->average_rating >= 4.7 && $this->completed_jobs >= 5);
    }

    public function scopeNeighborhoodTrusted($query) {
        return $query->where(function($q) {
            $q->whereHas('badges', fn($b) => $b->where('slug', 'neighborhood-favorite'))
              ->orWhere(function($sub) {
                  $sub->where('average_rating', '>=', 4.7)
                      ->where('completed_jobs', '>=', 5);
              });
        });
    }
}
