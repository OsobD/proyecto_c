<?php

namespace App\Livewire;

use App\Models\Salida;
use App\Models\Traslado;
use Livewire\Component;

/**
 * Componente DetalleRequisicion
 *
 * Muestra el detalle completo de una requisición (Salida o Traslado)
 */
class DetalleRequisicion extends Component
{
    public $tipo;
    public $id;
    public $requisicion;
    public $esConsumible;
    public $tipoNombre;
    public $tipoColor;
    public $persona;
    public $bodega;
    public $correlativo;
    public $descripcion;

    public function mount($tipo, $id)
    {
        $this->tipo = $tipo;
        $this->id = $id;

        if ($this->tipo === 'salida') {
            $this->requisicion = Salida::with(['persona', 'bodega', 'detalles.producto', 'detalles.lote'])
                ->findOrFail($this->id);
            $this->esConsumible = false;
            $this->tipoNombre = 'Salida - Productos No Consumibles';
            $this->tipoColor = 'blue';
            $this->persona = $this->requisicion->persona
                ? $this->requisicion->persona->nombres . ' ' . $this->requisicion->persona->apellidos
                : 'N/A';
            $this->bodega = $this->requisicion->bodega ? $this->requisicion->bodega->nombre : 'N/A';
            $this->correlativo = $this->requisicion->ubicacion;
            $this->descripcion = $this->requisicion->descripcion;
        } else {
            $this->requisicion = Traslado::with(['persona', 'bodegaOrigen', 'detalles.producto', 'detalles.lote'])
                ->findOrFail($this->id);
            $this->esConsumible = true;
            $this->tipoNombre = 'Traslado - Productos Consumibles';
            $this->tipoColor = 'amber';
            $this->persona = $this->requisicion->persona
                ? $this->requisicion->persona->nombres . ' ' . $this->requisicion->persona->apellidos
                : 'N/A';
            $this->bodega = $this->requisicion->bodegaOrigen ? $this->requisicion->bodegaOrigen->nombre : 'N/A';
            $this->correlativo = $this->requisicion->no_requisicion ?? $this->requisicion->correlativo;
            $this->descripcion = $this->requisicion->observaciones;
        }
    }

    public function render()
    {
        return view('livewire.detalle-requisicion');
    }
}
