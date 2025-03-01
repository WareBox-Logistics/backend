<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    use HasFactory;

    protected $table = "truck";

    protected $fillable = [
        'plates',
        'vin',
        'brand',
        'model',
        'driver'
    ];

    public function driver() {
        return $this->belongsTo(Employee::class);
    }

    public function deliveries(){
        return $this->hasMany(Delivery::class);
    }

    public function dockAssigments(){
        return $this->hasMany(DockAssignment::class);
    }
    
}
