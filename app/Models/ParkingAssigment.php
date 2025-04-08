<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingAssigment extends Model
{
    use HasFactory;

    protected $table = 'parkingAssigment';

    protected $fillable = [
        'delivery',
        'lot',
        'status',
        'arrival',
        'exit'
    ];

    public function delivery(){
        return $this->belongsTo(Delivery::class);
    } 

    public function lot(){
        return $this->belongsTo(Lot::class);
    }

    public function parkingLot()
{
    return $this->belongsTo(ParkingLot::class);
}
  
}
