<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dock extends Model
{
    use HasFactory;

    protected $table = 'dock';

    protected $fillable = [
        'status',
        'type',
        'warehouse',
        'number'
    ];

    const STATUS_AVAILABLE = 'Available';
    const STATUS_OCCUPIED = 'Occupied';
    const STATUS_LOADING = 'Loading'; 
    const STATUS_MAINTENANCE = 'Maintenance';

    const TYPE_LOADING = 'Loading';
    const TYPE_UNLOADING = 'Unloading';

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse');
    }

    public function dockAssignments()
    {
        return $this->hasMany(DockAssignment::class, 'dock');
    }
}
