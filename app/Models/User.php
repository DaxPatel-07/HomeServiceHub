<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use HasFactory, Notifiable;

    protected $fillable = ['name','email','password','role','phone','address','city','state','avatar','is_active'];
    protected $hidden = ['password','remember_token'];
    protected $casts = ['email_verified_at' => 'datetime', 'password' => 'hashed', 'is_active' => 'boolean'];

    public function provider() { return $this->hasOne(Provider::class); }
    public function bookings() { return $this->hasMany(Booking::class, 'customer_id'); }
    public function reviews() { return $this->hasMany(Review::class, 'customer_id'); }
    public function reminders() { return $this->hasMany(MaintenanceReminder::class, 'customer_id'); }
    public function complaints() { return $this->hasMany(Complaint::class, 'customer_id'); }
    public function splitBookings() { return $this->hasMany(SplitBooking::class, 'customer_id'); }

    public function isCustomer() { return $this->role === 'customer'; }
    public function isProvider() { return $this->role === 'provider'; }
    public function isAdmin() { return $this->role === 'admin'; }
    public function isMasterAdmin() { return $this->role === 'master_admin'; }
    public function isAdminOrMaster() { return in_array($this->role, ['admin','master_admin']); }
}
