<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = "category";

    protected $fillable = [
        'name',
        'description',
        'company'
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function products() {
        return $this->hasMany(Product::class);
    }
}
