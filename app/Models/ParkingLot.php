<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingLot extends Model
{
    use HasFactory;

    protected $table = 'parking_lots';

    protected $fillable = [
        'name',
        'warehouse_id',
        'rows',
        'columns'
    ];
    
    public function warehouse(){
        return $this->belongsTo(Warehouse::class);
    }
    
    public function lots()
    {
        return $this->hasMany(Lot::class, 'parking_lot_id');
    }

}
