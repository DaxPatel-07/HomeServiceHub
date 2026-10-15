<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SplitBookingItem extends Model {
    protected $fillable = ['split_booking_id','provider_id','category_id','scheduled_time','amount','status'];
    public function splitBooking() { return $this->belongsTo(SplitBooking::class); }
    public function provider() { return $this->belongsTo(Provider::class); }
    public function category() { return $this->belongsTo(ServiceCategory::class, 'category_id'); }
}
