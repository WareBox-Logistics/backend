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
        'delivery_id',
        'status',
        'scheduled_time',
         'duration_minutes'
    ];

    protected $casts = [
        'scheduled_time' => 'datetime',
    ];

    public function dock()
    {
        return $this->belongsTo(Dock::class, 'dock', 'id');
    }

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }
    
}
