<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceCategory extends Model {
    use HasFactory;
    protected $fillable = ['name','slug','description','icon','color','is_active'];

    public function providers() { return $this->hasMany(Provider::class, 'category_id'); }
    public function bookings() { return $this->hasMany(Booking::class, 'category_id'); }
    public function reminders() { return $this->hasMany(MaintenanceReminder::class, 'category_id'); }
}
