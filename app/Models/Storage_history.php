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
}
