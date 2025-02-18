<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pallet extends Model
{
    use HasFactory;

    protected $table = 'pallet';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'company',
        'warehouse',
        'weight',
        'volume',
        'status',
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
}
