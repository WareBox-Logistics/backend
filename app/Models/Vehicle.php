<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $table = 'vehicles';

    protected $fillable = [
      'plates',
      'vin',
      'model_id',
      'volume',
      'driver_id',
        'type',
        'is_available'
    ];

    public function modell(){
        return $this->belongsTo(Modell::class);
    }
    public function deliveries(){
        return $this->hasMany(Delivery::class);
    }
}
