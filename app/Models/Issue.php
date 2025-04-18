<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use HasFactory;

    protected $table = "issue";

    protected $fillable = [
        'status',
        'description',
        'report',
        'operator',
        'support'
    ];  

    public function report(){
        return $this->belongsTo(Report::class, 'report');
    }

    public function operator(){
        return $this->belongsTo(Employee::class, 'operator');
    }

    public function supports(){
        return $this->hasMany(Support::class,'issue');
    }
}
