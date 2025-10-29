<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegimenTributario extends Model
{
    use HasFactory;

    protected $table = 'regimen_tributario';

    protected $fillable = [
        'nombre',
    ];

    public $timestamps = false;

    // Relaciones
    public function proveedores()
    {
        return $this->hasMany(Proveedor::class, 'id_regimen');
    }
}
