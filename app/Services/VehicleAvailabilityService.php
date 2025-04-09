<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\VehicleAvailability;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
  // In app/Services/VehicleAvailabilityService.php

  public function getAvailableVehicles($startDate, $endDate, $type = null, $origin = null)
  {
      $query = Vehicle::query();
      
      // Filter by type
      if ($type) {
          $vehicleType = $type === 'truck' ? 'semi_truck' : 'trailer';
          $query->where('type', $vehicleType);
      }
  
      // Time availability check (simplified)
      $query->whereDoesntHave('availability', function($q) use ($startDate, $endDate) {
          $q->where(function($q) use ($startDate, $endDate) {
              $q->where('end_date', '>', $startDate)
                ->where('start_date', '<', $endDate);
          });
      });
  
      if ($origin instanceof \App\Models\Warehouse) {
          $query->where(function($q) use ($origin, $startDate) {
              // Vehicles parked at warehouse
              $q->whereHas('currentParkingLot', function($sub) use ($origin) {
                  $sub->whereHas('parkingLot', function($lot) use ($origin) {
                      $lot->where('warehouse_id', $origin->id);
                  });
              });
              
              // OR vehicles arriving before start time
              $q->orWhereHas('truckDeliveries', function($sub) use ($origin, $startDate) {
                  $sub->where('destination_id', $origin->id)
                      ->where('destination_type', get_class($origin))
                      ->where('estimated_arrival', '<=', $startDate);
              });
          });
      }
  
      return $query->get();
  }
    
    protected function findVehiclesNaturallyAtOrigin($query, $origin, $startDate)
    {
        return (clone $query)->where(function($q) use ($origin, $startDate) {
            // Vehicles arriving at origin before start date
            $q->whereHas('truckDeliveries', function($sub) use ($origin, $startDate) {
                $sub->where('destination_id', $origin->id)
                    ->where('destination_type', get_class($origin))
                    ->where('estimated_arrival', '<=', $startDate)
                    ->whereDoesntHave('nextDelivery');
            });
            
            // Include trailers if needed
            $q->orWhereHas('trailerDeliveries', function($sub) use ($origin, $startDate) {
                $sub->where('destination_id', $origin->id)
                    ->where('destination_type', get_class($origin))
                    ->where('estimated_arrival', '<=', $startDate);
            });
        })->get();
    }
    
    protected function findVehiclesParkedAtOrigin($query, $warehouse)
    {
        return (clone $query)->whereHas('activeParkingAtWarehouse', function($q) use ($warehouse) {
            $q->whereHas('lot.parkingLot', function($sub) use ($warehouse) {
                $sub->where('warehouse_id', $warehouse->id);
            });
        })->get();
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
    public function releaseVehicle(int $vehicleId, int $deliveryId): bool//check if this is veing released once the delivery is done or with what aditional logic
    {
        return (bool) VehicleAvailability::where('vehicle_id', $vehicleId)
            ->where('related_delivery_id', $deliveryId)
            ->delete();
    }
}