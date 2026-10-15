<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model {
    use HasFactory;
    protected $fillable = ['booking_id','customer_id','provider_id','rating','comment','is_visible'];
    protected $casts = ['is_visible' => 'boolean'];
    public function booking() { return $this->belongsTo(Booking::class); }
    public function customer() { return $this->belongsTo(User::class, 'customer_id'); }
    public function provider() { return $this->belongsTo(Provider::class); }
}
