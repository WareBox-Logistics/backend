<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class support extends Model
{
    use HasFactory;

    protected $table = "support";

    protected $fillable = [
        'description',
        'issue',
        'status',
    ];
    
    
}
