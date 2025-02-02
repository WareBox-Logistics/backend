<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = "service";

    protected $fillable = [
        'type'
    ];

    public function companies() {
        return $this->hasMany(Company::class);
    }

}
