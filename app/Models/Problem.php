<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{
    protected $table = "problem";

    protected $fillable = [
        'name',
        'level',
    ];
}
