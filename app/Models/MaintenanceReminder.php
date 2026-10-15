<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MaintenanceReminder extends Model {
    protected $fillable = ['customer_id','category_id','title','notes','next_due_date','repeat_months','is_active'];
    protected $casts = ['next_due_date' => 'date', 'is_active' => 'boolean'];
    public function customer() { return $this->belongsTo(User::class, 'customer_id'); }
    public function category() { return $this->belongsTo(ServiceCategory::class, 'category_id'); }
    public function isDueSoon() { return $this->next_due_date->diffInDays(now()) <= 7 && $this->next_due_date->isFuture(); }
}
