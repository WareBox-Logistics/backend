<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'truck',
        'trailer',
        'company',
        'created_by',
        'status',
        'date_created',
        'finished_date'
    ];

    public function truck() {
        return $this->belongsTo(Truck::class);
    }

    public function trailer() {
        return $this->belongsTo(Trailer::class);
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function created_by(){
        return $this->belongsTo(Employee::class);
    }
}
