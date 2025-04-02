<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\VehicleAvailability;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class VehicleAvailabilityService
{
    /**
     * Check if a vehicle is available for given dates
     */
    public function checkAvailability(int $vehicleId, string $startDate, string $endDate): bool
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        return !VehicleAvailability::where('vehicle_id', $vehicleId)
            ->where(function($query) use ($start, $end) {
                $query->where(function($q) use ($start, $end) {
                    // Existing reservation starts during requested period
                    $q->where('start_date', '>=', $start)
                      ->where('start_date', '<', $end);
                })->orWhere(function($q) use ($start, $end) {
                    // Existing reservation ends during requested period
                    $q->where('end_date', '>', $start)
                      ->where('end_date', '<=', $end);
                })->orWhere(function($q) use ($start, $end) {
                    // Existing reservation completely covers requested period
                    $q->where('start_date', '<=', $start)
                      ->where('end_date', '>=', $end);
                })->orWhere(function($q) use ($start, $end) {
                    // Requested period completely covers existing reservation
                    $q->where('start_date', '>=', $start)
                      ->where('end_date', '<=', $end);
                });
            })
            ->exists();
    }

    /**
     * Get all available vehicles for given date range
     */
    //this is not  filtering correctly the vehicles that are occupied at that time
    public function getAvailableVehicles(string $startDate, string $endDate, ?string $type = null)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        $query = Vehicle::query();
        
        if ($type) {
            $vehicleType = $type === 'truck' ? 'semi_truck' : 'trailer';
            $query->where('type', $vehicleType);
        }
    
        return $query->whereDoesntHave('availability', function($q) use ($start, $end) {
                $q->where(function($q) use ($start, $end) {
                    $q->where(function($q) use ($start, $end) {
                        // Existing reservation starts during requested period
                        $q->where('start_date', '>=', $start)
                          ->where('start_date', '<', $end);
                    })->orWhere(function($q) use ($start, $end) {
                        // Existing reservation ends during requested period
                        $q->where('end_date', '>', $start)
                          ->where('end_date', '<=', $end);
                    })->orWhere(function($q) use ($start, $end) {
                        // Existing reservation completely covers requested period
                        $q->where('start_date', '<=', $start)
                          ->where('end_date', '>=', $end);
                    })->orWhere(function($q) use ($start, $end) {
                        // Requested period completely covers existing reservation
                        $q->where('start_date', '>=', $start)
                          ->where('end_date', '<=', $end);
                    });
                });
            })
            ->get();
    }

    /**
     * Reserve a vehicle for specific dates
     */
    public function reserveVehicle(
        int $vehicleId, 
        string $startDate, 
        string $endDate, 
        string $type = 'delivery', 
        ?int $deliveryId = null
    ): VehicleAvailability {

        Log::info('Attempting to reserve vehicle', [
            'vehicle_id' => $vehicleId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'type' => $type,
            'delivery_id' => $deliveryId
        ]);
        $availability = VehicleAvailability::create([
            'vehicle_id' => $vehicleId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'type' => $type,
            'related_delivery_id' => $deliveryId,
            'reason' => 'Scheduled delivery'
        ]);
        Log::info('Vehicle reservation created', [
            'reservation_id' => $availability->id,
            'details' => $availability->toArray()
        ]);
        return $availability;
    }

    /**
     * Release a vehicle reservation
     */
    public function releaseVehicle(int $vehicleId, int $deliveryId): bool
    {
        return (bool) VehicleAvailability::where('vehicle_id', $vehicleId)
            ->where('related_delivery_id', $deliveryId)
            ->delete();
    }
}