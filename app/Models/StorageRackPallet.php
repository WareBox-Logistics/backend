<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StorageRackPallet extends Model
{
    use HasFactory;

    protected $table = 'storage_rack_pallet';

    protected $primaryKey = ['pallet', 'rack'];

    public $incrementing = false;

    protected $fillable = [
        'pallet',
        'rack',
        'warehouse',
        'position',
        'level',
        'stored_at',
        'status',
    ];

    public function pallet()
    {
        return $this->belongsTo(Pallet::class, 'pallet');
    }

    public function rack()
    {
        return $this->belongsTo(Rack::class, 'rack');
    }

    public function warehouse(){
        return $this->belongsTo(Warehouse::class);
    }
}
