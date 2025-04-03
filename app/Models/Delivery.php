<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $table = "delivery";

    protected $fillable = [
        'type',
        'truck',
        'trailer',
        'company',
        'created_by',
        'status',
        'origin_id',
        'origin_type',
        'destination_id',
        'destination_type',
        'shipping_date',
        'estimated_arrival',
        'estimated_duration_minutes',
        'completed_date',
        'route'
    ];

    protected $dates = [
        'shipping_date',
        'estimated_arrival',
        'completed_date'
    ];

    protected $casts = [
        'route' => 'array',
        'shipping_date' => 'datetime',
        'estimated_arrival' => 'datetime',
    ];

    // Delivery types
    const TYPE_WAREHOUSE_TO_LOCATION = 'warehouse_to_location';
    const TYPE_LOCATION_TO_WAREHOUSE = 'location_to_warehouse';
    const TYPE_WAREHOUSE_TO_WAREHOUSE = 'warehouse_to_warehouse';
    const TYPE_LOCATION_TO_LOCATION = 'location_to_location';

    public static $types = [
        self::TYPE_WAREHOUSE_TO_LOCATION => 'Warehouse to Location',
        self::TYPE_LOCATION_TO_WAREHOUSE => 'Location to Warehouse',
        self::TYPE_WAREHOUSE_TO_WAREHOUSE => 'Warehouse to Warehouse',
        self::TYPE_LOCATION_TO_LOCATION => 'Location to Location',
    ];

    // Statuses
    const STATUS_PENDING = 'Pending';
    const STATUS_DOCKING = 'Docking';
    const STATUS_LOADING = 'Loading';
    const STATUS_DELIVERING = 'Delivering';
    const STATUS_EMPTYING = 'Emptying';
    const STATUS_DELIVERED = 'Delivered'; 
    
    public static $statuses = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_DOCKING => 'Docking',
        self::STATUS_LOADING => 'Loading',
        self::STATUS_DELIVERING => 'Delivering',
        self::STATUS_EMPTYING => 'Emptying',
        self::STATUS_DELIVERED => 'Delivered', 
    ];

    public function truck()
    {
        return $this->belongsTo(Vehicle::class, 'truck');
    }

    public function trailer()
    {
        return $this->belongsTo(Vehicle::class, 'trailer');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company');
    }

    public function origin()
    {
        return $this->morphTo();
    }

    public function destination()
    {
        return $this->morphTo();
    }


    public function employee() {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function deliveryDetails()
    {
        // Specify the foreign key column on the delivery_detail table
        return $this->hasMany(DeliveryDetail::class, 'delivery');
    }

    public function parkingAssigments(){
        return $this->hasMany(ParkingAssigment::class);
    }
    public function isOutbound(): bool
    {
        return $this->origin_type === Warehouse::class && 
               $this->destination_type === Location::class;
    }

    public function isInbound(): bool
    {
        return $this->origin_type === Location::class && 
               $this->destination_type === Warehouse::class;
    }

    public function calculateDuration()
    {
        if ($this->shipping_date && $this->estimated_arrival) {
            $this->estimated_duration_minutes = $this->shipping_date->diffInMinutes($this->estimated_arrival);
        }
    }

    public function dockAssignment()
    {
        return $this->hasOne(DockAssignment::class);
    }

}
