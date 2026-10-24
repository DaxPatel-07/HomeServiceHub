<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model {
    use HasFactory;
    protected $fillable = ['booking_code','customer_id','provider_id','category_id','booking_date','booking_time','address','city','description','service_charge','total_amount','payment_method','payment_status','status','cancellation_reason','is_split','split_booking_id'];
    protected $casts = ['booking_date' => 'date', 'is_split' => 'boolean', 'total_amount' => 'float'];

    public function customer() { return $this->belongsTo(User::class, 'customer_id'); }
    public function provider() { return $this->belongsTo(Provider::class, 'provider_id'); }
    public function category() { return $this->belongsTo(ServiceCategory::class, 'category_id'); }
    public function review() { return $this->hasOne(Review::class); }
    public function complaint() { return $this->hasOne(Complaint::class); }
    public function splitBooking() { return $this->belongsTo(SplitBooking::class, 'split_booking_id'); }

    public static function generateCode() { return 'BK-' . strtoupper(uniqid()); }
    public function getStatusBadgeAttribute() {
        return match($this->status) {
            'pending' => 'warning', 'accepted' => 'info', 'in_progress' => 'primary',
            'completed' => 'success', 'cancelled' => 'danger', default => 'secondary'
        };
    }
    public function getFormattedTimeAttribute(): string {
        if (!$this->booking_time) return '';
        try {
            return \Carbon\Carbon::parse($this->booking_time)->format('h:i A');
        } catch (\Exception $e) {
            return (string) $this->booking_time;
        }
    }
}
