<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'origin',
        'destination'
    ];

    public function originLocation() {
        return $this->belongsTo(Location::class);
    }

    public function destinationLocation() {
        return $this->belongsTo(Location::class);
    }
}
