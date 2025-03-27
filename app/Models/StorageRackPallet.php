<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StorageRackPallet extends Model
{
    use HasFactory;

    protected $table = 'storage_rack_pallet';

    // En claves compuestas, se sugiere ponerlo a null
    // Y especificar $incrementing = false
    protected $primaryKey = ['pallet', 'rack'];
    public $incrementing = false;

    // Si tus columnas son string, establece $keyType = 'string';
    // protected $keyType = 'string'; // solo si corresponde

    protected $fillable = [
        'pallet',
        'rack',
        'position',
        'level',
        'stored_at',
        'status',
    ];

    /**
     * Para indicarle a Eloquent que use las columnas 'pallet' y 'rack' 
     * como la condición WHERE al hacer un update() o save().
     */
    protected function setKeysForSaveQuery($query)
    {
        // Le dices a Eloquent cómo ubicar la fila:
        return $query
            ->where('pallet', $this->getAttribute('pallet'))
            ->where('rack',   $this->getAttribute('rack'));
    }
    protected function performDeleteOnModel()
    {
        if ($this->fireModelEvent('deleting') === false) {
            return false;
        }

        $this->setKeysForSaveQuery($this->newModelQuery())->delete();

        // Cambiamos a false para indicar que ya no existe
        $this->exists = false;

        $this->fireModelEvent('deleted', false);

        return true;
    }
    public function pallet()
    {
        return $this->belongsTo(Pallet::class, 'pallet');
    }

    public function rack()
    {
        return $this->belongsTo(Rack::class, 'rack');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
