<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryDetail extends Model
{
    use HasFactory;

    protected $fillable =[
        'delivery',
        'product',
        'qty'
    ];

    public function delivery() {
        return $this->belongsTo(Delivery::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
