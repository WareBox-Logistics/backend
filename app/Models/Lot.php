<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    use HasFactory;

    protected $table = 'lot';

    protected $fillable = [
        'spot_code',
        'parking_lot',
        'is_occupied',
        'allowed_type'
    ];

    public function parkingLot(){
        return $this->belongsTo(ParkingLot::class);
    }

    public function parkingAssigments(){
        return $this->hasMany(ParkingAssigment::class);
    }
    
    
}
