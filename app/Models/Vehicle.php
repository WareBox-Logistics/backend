<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $table = 'vehicle';

    protected $fillable = [
      'plates',
      'vin',
      'model',
      'volume',
      'driver'
    ];

    public function modell(){
        return $this->belongsTo(Modell::class);
    }
    public function deliveries(){
        return $this->hasMany(Delivery::class);
    }
}
