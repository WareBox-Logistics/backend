<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class problem extends Model
{
    protected $table = "problem";

    protected $fillable = [
        'name',
        'level',
    ];
}
