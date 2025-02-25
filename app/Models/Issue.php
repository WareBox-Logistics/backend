<?php

namespace App\Models;

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
    ];  

    public function report(){
        return $this->belongsTo(Report::class, 'report');
    }
}
