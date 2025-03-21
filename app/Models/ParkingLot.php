<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingLot extends Model
{
    use HasFactory;

    protected $table = 'parkingLot';

    protected $fillable = [
        'name',
        'warehouse',
        'capacity',
    ];
    
    public function warehouse(){
        return $this->belongsTo(Warehouse::class);
    }

}
