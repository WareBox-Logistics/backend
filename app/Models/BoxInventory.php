<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BoxInventory extends Model
{

        use HasFactory;
    
        protected $table = 'box_inventory';
    
        protected $primaryKey = 'id';
    
        public $incrementing = true;
    
        protected $keyType = 'int';
    
        public $timestamps = true;
    
        protected $fillable = [
            'qty',
            'weight',
            'volume',
            'pallet',
            'inventory',
        ];
    
        public function pallet()
        {
            return $this->belongsTo(Pallet::class, 'pallet');
        }
    
        public function inventory()
        {
            return $this->belongsTo(Inventory::class, 'inventory');
        
}

}