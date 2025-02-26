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
        'support',
        'operator'
    ];  

    public function report(){
        return $this->belongsTo(Report::class, 'report');
    }
}
