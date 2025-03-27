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
        'image',
        'company',
        'category'
    ];

    public function company() {
        return $this->belongsTo(Company::class, 'company');
    }

    public function category() {
        return $this->belongsTo(Category::class, 'category');
    }

    public function inventories() {
        return $this->hasMany(Inventory::class);
    }

}
