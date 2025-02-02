<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = "product";

    protected $fillable = [
        'name',
        'description',
        'sku',
        'price',
        'company',
        'category'
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function inventory() {
        return $this->hasMany(Inventory::class);
    }

    public function deliveries() {
        return $this->belongsToMany(Delivery::class, 'delivery_detail')
                    ->withPivot('qty');
    }
}
