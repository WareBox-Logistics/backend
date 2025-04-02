<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    use HasFactory;

    protected $table = "support";

    protected $fillable = [
        'description',
        'issue',
        'status',
        'operator'
    ];
    
    public function issue(){
        return $this->belongsTo(Issue::class, 'issue');
    }

    public function operator(){
        return $this->belongsTo(Employee::class, 'operator');
    }
    
}
