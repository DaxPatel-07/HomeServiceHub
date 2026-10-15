<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model {
    protected $fillable = ['name','slug','description','icon','color'];
    public function providers() { return $this->belongsToMany(Provider::class, 'provider_badges')->withPivot('earned_at')->withTimestamps(); }
}
