<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

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
}
