<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trailer extends Model
{
    use HasFactory;

    protected $table = 'trailer';

    protected $fillable = [
        'plates',
        'vin',
        'volume',
        'brand',
    ];
    
    public function deliveries(){
        return $this->hasMany(Delivery::class);
    }
}
