<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    protected $table = 'employee';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'warehouse'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function truck() {
        return $this->hasOne(Vehicle::class, 'driver_id');
    }

    public function deliveries(){
        return $this->hasMany(Delivery::class);
    }

    public function issues(){
        return $this->hasMany(Issue::class);
    }
    public function supports(){
        return $this->hasMany(Support::class);
    }
    public function warehouse(){
        return $this->belongsTo(Warehouse::class);
    }

    public function reports(){
        return $this->belongsTo(Report::class);
    }
}
