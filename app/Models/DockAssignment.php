<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DockAssignment extends Model
{
    use HasFactory;

    protected $table = 'dock_assignment';
    
    protected $fillable = [
        'dock',
        'truck',
        'status',
        'scheduled_time',
         'duration_minutes'
    ];

    public function dock()
    {
        return $this->belongsTo(Dock::class, 'dock');
    }

    public function truck()
    {
        return $this->belongsTo(Vehicle::class, 'truck');
    }
}
