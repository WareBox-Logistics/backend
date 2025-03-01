<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = 'report';

    protected $fillable = [
       'route',
       'ubication',
       'issue',
       'description',
       'driver'
    ];

    public function issues(){
        return $this->hasMany(Issue::class);
    }

    public function problem(){
        return $this->belongsTo(Problem::class);
    }

    public function driver(){
        return $this->belongsTo(Employee::class);
    }
}
