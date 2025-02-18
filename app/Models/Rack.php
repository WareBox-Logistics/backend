<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rack extends Model
{
    use HasFactory;

    protected $table = 'rack';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'warehouse',
        'section',
        'level',
        'status',
        'capacity_volume',
        'used_volume',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse');
    }

    public function storageRackPallets()
    {
        return $this->hasMany(StorageRackPallet::class, 'rack');
    }
}

