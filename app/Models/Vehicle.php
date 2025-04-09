<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Delivery;
use App\Models\Modell;
use App\Models\ParkingAssigment;
use Illuminate\Support\Facades\DB;

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
//  public function deliveries()
// {
//     return Delivery::where(function($q) {
//         $q->where('truck', $this->id)
//           ->orWhere('trailer', $this->id);
//     });
// }

// public function allDeliveries()
// {
//     return Delivery::where(function ($q) {
//         $q->where('truck', $this->id)
//           ->orWhere('trailer', $this->id);
//     });
// }

public function truckDeliveries()
{
    return $this->hasMany(Delivery::class, 'truck');
}

public function trailerDeliveries()
{
    return $this->hasMany(Delivery::class, 'trailer');
}

    // public function deliveries()
    // {
    //     return $this->hasMany(Delivery::class, 'truck', 'id')
    //         ->orWhere('trailer', $this->id);
    // }

    public function getDeliveriesAttribute()
{
    return Delivery::where('truck', $this->id)
        ->orWhere('trailer', $this->id)
        ->get();
}

public function activeAvailability()
{
    return $this->hasOne(VehicleAvailability::class, 'vehicle_id')
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->latest();
}

    public function availability()
    {
        return $this->hasMany(VehicleAvailability::class, 'vehicle_id');
    }

    public function getActiveAvailabilityAttribute()
    {
        return $this->availability()
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }

public function driver()
{
    return $this->belongsTo(Employee::class, 'driver_id');
}

public function nextDelivery()
{
    return $this->hasOne(Delivery::class, 'truck', 'id')
        ->where('shipping_date', '>', DB::raw('NOW()'))
        ->orderBy('shipping_date');
}

public function activeParkingAtWarehouse()
{
    return $this->hasOne(ParkingAssigment::class, 'vehicle_id')
        ->where(function ($q) {
            $q->where('exit', '>', now())
              ->orWhereNull('exit');
        })
        ->latest();
}

public function allDeliveries()
{
    return Delivery::where(function ($q) {
        $q->where('truck', $this->id)
          ->orWhere('trailer', $this->id);
    });
}

// Removed duplicate method to avoid redeclaration error
public function currentParkingLot()
{
    return $this->hasOne(Lot::class, 'vehicle_id')
        ->where('is_occupied', true)
        ->with('parkingLot.warehouse');
}

public function scopeAvailableBetween($query, $start, $end)
{
    return $query->whereDoesntHave('availability', function($q) use ($start, $end) {
        $q->where(function($q) use ($start, $end) {
            $q->where('start_date', '<', $end)
              ->where('end_date', '>', $start);
        });
    });
}

// Add this helper method
public function isParkedAtWarehouse($warehouseId)
{
    return $this->currentParkingLot()
        ->whereHas('parkingLot', function($q) use ($warehouseId) {
            $q->where('warehouse_id', $warehouseId);
        })
        ->exists();
}

}
