<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $table = "delivery";

    protected $fillable = [
        'truck',
        'trailer',
        'company',
        'created_by',
        'status',
        'origin',
        'destination',
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

    public function location(){
        return $this->belongsTo(Location::class);
    }

    //is this necessary?
    public function employee() {
        return $this->belongsTo(Employee::class);
    }

    public function deliveryDetail(){
        return $this->hasMany(DeliveryDetail::class);
    }

}
