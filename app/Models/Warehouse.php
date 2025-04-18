<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $table = "warehouse";

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'id_routing_net',
        'source',
        'target'
    ];

    public function inventories() {
        return $this->hasMany(Inventory::class);
    }

    public function pallets()
    {
        return $this->hasMany(Pallet::class, 'warehouse');
    }

    public function racks()
    {
        return $this->hasMany(Rack::class, 'warehouse');
    }

    public function docks()
    {
        return $this->hasMany(Dock::class, 'warehouse');
    }

    public function storageHistories(){
        return $this->hasMany(Storage_history::class);
    }

    public function storageRackPallets(){
        return $this->hasMany(StorageRackPallet::class);
    }
    public function employees(){
        return $this->hasMany(Employee::class);
    }

    public function parkingLots(){
        return $this->hasMany(ParkingLot::class);
    }
}
