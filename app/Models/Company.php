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
        return $this->belongsTo(Service::class);
    }

    public function users() {
        return $this->hasMany(User::class);
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
}
