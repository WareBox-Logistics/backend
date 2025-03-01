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
        'qty'
    ];

    public function delivery() {
        return $this->belongsTo(Delivery::class);
    }

    public function pallet(){
        return $this->belongsTo(Pallet::class);
    }
}
