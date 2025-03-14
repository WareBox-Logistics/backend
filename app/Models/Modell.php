<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modell extends Model
{
    use HasFactory;

    protected $table = 'modell';

    protected $fillable = [
        'brand',
        'truck',
        'year',
    ];

    public function brand(){
        return $this->belongsTo(Brand::class);
    }
    
    public function vehicles(){
        return $this->hasMany(Vehicle::class);
    }
}
