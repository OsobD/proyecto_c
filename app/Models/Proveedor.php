<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedor';

    protected $fillable = [
        'nit',
        'id_regimen',
        'nombre',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public $timestamps = false;

    // Relaciones
    public function regimenTributario()
    {
        return $this->belongsTo(RegimenTributario::class, 'id_regimen');
    }

    public function compras()
    {
        return $this->hasMany(Compra::class, 'id_proveedor');
    }

    // Reglas de validación
    public static function rules($id = null)
    {
        return [
            'nit' => 'required|numeric|digits_between:5,20|unique:proveedor,nit' . ($id ? ",$id" : ''),
            'id_regimen' => 'required|exists:regimen_tributario,id',
            'nombre' => 'required|string|min:3|max:255',
            'activo' => 'nullable|boolean',
        ];
    }
}
