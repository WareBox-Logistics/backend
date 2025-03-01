<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rack extends Model
{
    use HasFactory;

    protected $table = 'rack';

    protected $fillable = [
        'warehouse',
        'section',
        'height',
        'width',
        'depth',
        'status',
        'capacity_volume',
        'used_volume',
        'capacity_weight',
        'used_weight',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse');
    }

    public function storageRackPallets()
    {
        return $this->hasMany(StorageRackPallet::class, 'rack');
    }

    public function storageHistories(){
        return $this->hasMany(Storage_history::class);
    }
}

