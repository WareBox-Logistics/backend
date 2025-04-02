<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleAvailability extends Model
{
    use HasFactory;

    protected $table = 'vehicle_availability';

    protected $fillable = [
        'vehicle_id',
        'start_date',
        'end_date',
        'type',
        'reason',
        'related_delivery_id'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function delivery()
    {
        return $this->belongsTo(Delivery::class, 'related_delivery_id');
    }
}
