<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = "inventory";

    protected $fillable = [
        'warehouse',
        'product',
        'qty'
    ];

    public function warehouse() {
        return $this->belongsTo(Warehouse::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function boxInventories()
    {
        return $this->hasMany(BoxInventory::class, 'inventory');
    }

}
