<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Delivery;
use App\Models\Modell;
use App\Models\ParkingAssigment;

class Vehicle extends Model
{
    use HasFactory;

    protected $table = 'vehicles';

    protected $fillable = [
      'plates',
      'vin',
      'model_id',
      'volume',
      'driver_id',
        'type',
        'is_available'
    ];

    public function modell(){
        return $this->belongsTo(Modell::class, 'model_id');
    }
    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'truck', 'id')
            ->orWhere('trailer', 'id');
    }

    // public function deliveries()
    // {
    //     return $this->hasMany(Delivery::class, 'truck', 'id')
    //         ->orWhere('trailer', $this->id);
    // }

    public function availability()
    {
        return $this->hasMany(VehicleAvailability::class, 'vehicle_id')
            ->from('vehicle_availability');
    }

    public function activeAvailability()
    {
        return $this->availability()
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
}

public function driver()
{
    return $this->belongsTo(Employee::class, 'driver_id');
}

public function activeParking()
{
    return $this->hasOne(ParkingAssigment::class)
               ->where(function($q) {
                   $q->where('end_time', '>', now())
                     ->orWhereNull('end_time');
               })
               ->latest();
}

public function nextDelivery()
{
    return $this->hasOne(Delivery::class, 'truck', 'id')
               ->where('shipping_date', '>', now())
               ->orderBy('shipping_date');
}
}
