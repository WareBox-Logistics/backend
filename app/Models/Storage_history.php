<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Storage_history extends Model
{
    use HasFactory;
    
    protected $table = 'storage_history';        
    protected $fillable = [
        'pallet',
        'rack',
        'warehouse',
        'position',
        'level',
        'stored_at',
        'removed_at'
    ];

    public function pallet(){
        return $this->belongsTo(Pallet::class);
    }

    public function rack(){
        return $this->belongsTo(Rack::class);
    }

    public function warehouse(){
        return $this->belongsTo(Warehouse::class);
    }
}
