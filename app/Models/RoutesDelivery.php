<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoutesDelivery extends Model
{
    use HasFactory;

    protected $table = "routes_delivery";

    protected $fillable = [
        'delivery',
        'route',
        'isBackup'
    ];

    public function delivery() {
        return $this->belongsTo(Delivery::class);
    }

    public function route() {
        return $this->belongsTo(Route::class);
    }
}
