<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $table = "route";

    protected $fillable = [
        'origin',
        'destination',
        'polyline',
        'name',
        'company'
    ];

    public function originLocation() {
        return $this->belongsTo(Location::class);
    }

    public function destinationLocation() {
        return $this->belongsTo(Location::class);
    }

    public function deliveries() {
        return $this->belongsToMany(Delivery::class, 'routes_delivery')
                    ->withPivot('isBackup');
    }
}
