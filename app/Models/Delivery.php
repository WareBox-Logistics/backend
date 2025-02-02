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

    public function createdBy() {
        return $this->belongsTo(Employee::class);
    }

    public function products() {
        return $this->belongsToMany(Product::class, 'delivery_detail')
                    ->withPivot('qty');
    }

    public function routes() {
        return $this->belongsToMany(Route::class, 'routes_delivery')
                    ->withPivot('isBackup');
    }
}
