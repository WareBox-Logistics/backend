<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = "report";

    protected $fillable = [
        'route',
        'ubication',
        'issue',
        'description',
    ];  

    public function route(){
        return $this->belongsTo(Route::class, 'route');
    }

}
