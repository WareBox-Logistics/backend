<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dock extends Model
{
    use HasFactory;

    protected $table = 'dock';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'status',
        'type',
        'warehouse',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse');
    }

    public function dockAssignments()
    {
        return $this->hasMany(DockAssignment::class, 'dock');
    }
}
