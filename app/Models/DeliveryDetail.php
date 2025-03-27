<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryDetail extends Model
{
    use HasFactory;

    protected $table = "delivery_detail";

    protected $fillable =[
        'delivery',
        'pallet',
    ];

    public function delivery()
    {
        // Explicitly specify the foreign key column
        return $this->belongsTo(Delivery::class, 'delivery');
    }

    public function pallet(){
        return $this->belongsTo(Pallet::class, 'pallet');
    }
}
