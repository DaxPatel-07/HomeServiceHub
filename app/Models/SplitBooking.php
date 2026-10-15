<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SplitBooking extends Model {
    use HasFactory;
    protected $fillable = ['split_code','customer_id','booking_date','address','city','total_amount','payment_method','status'];
    protected $casts = ['booking_date' => 'date', 'total_amount' => 'float'];

    public function customer() { return $this->belongsTo(User::class, 'customer_id'); }
    public function items() { return $this->hasMany(SplitBookingItem::class); }
    public static function generateCode() { return 'SP-' . strtoupper(uniqid()); }
}
