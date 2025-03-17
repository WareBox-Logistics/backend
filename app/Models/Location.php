<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $table = "location";

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'company',
        'id_routing_net',
        'source',
        'target'
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function deliveries(){
        return $this->hasMany(Delivery::class);
    }

}
