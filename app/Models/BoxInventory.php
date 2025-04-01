<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BoxInventory extends Model
{

        use HasFactory;
    
        protected $table = 'box_inventory';        
        protected $fillable = [
            'pallet',
            'product',
            'qty',
            'weight',
            'height',
            'width',
            'dept',
            'volume',
        ];
    
        public function pallet()
        {
            return $this->belongsTo(Pallet::class, 'pallet');
        }
    
        public function product()
        {
            return $this->belongsTo(Product::class, 'product');
        }

}