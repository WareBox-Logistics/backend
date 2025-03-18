<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class zPallet extends Model
{
    use HasFactory;

    protected $table = 'pallet';

    protected $fillable = [
        'company',
        'warehouse',
        'weight',
        'volume',
        'status',
        'verified'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse');
    }

    public function boxInventories()
    {
        return $this->hasMany(BoxInventory::class, 'pallet');
    }

    public function storageRackPallets()
    {
        return $this->hasMany(StorageRackPallet::class, 'pallet');
    }

    public function deliveryDetail(){
        return $this->hasMany(DeliveryDetail::class);
    }

    public function storageHistories(){
        return $this->hasMany(Storage_history::class);
    }

}
