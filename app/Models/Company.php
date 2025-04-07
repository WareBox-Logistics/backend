<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'company';

    protected $fillable = [
        'name',
        'rfc',
        'email',
        'phone',
        'service'
    ];

    public function service() {
        return $this->belongsTo(Service::class, 'service');
    }

    public function users() {
        return $this->hasMany(UserBox::class); //User -> UserBox
    }

    public function categories() {
        return $this->hasMany(Category::class);
    }

    public function locations() {
        return $this->hasMany(Location::class);
    }
    
    public function pallets()
    {
        return $this->hasMany(Pallet::class, 'company');
    }

    public function deliveries(){
        return $this->hasMany(Delivery::class);
    }

    public function products(){
        return $this->hasMany(Product::class);
    }

}
