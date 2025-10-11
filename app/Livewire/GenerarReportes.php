<?php

namespace App\Livewire;

use Livewire\Component;

class GenerarReportes extends Component
{
    public $tiposReporte = [];
    public $usuarios = [];

    public function mount()
    {
        $this->tiposReporte = [
            'inventario_bodega' => 'Inventario de Bodega',
            'tarjeta_responsabilidad' => 'Tarjeta de Responsabilidad',
            'movimientos' => 'Movimientos de Producto',
        ];

        $this->usuarios = [
            ['id' => 1, 'nombre' => 'Juan Pérez'],
            ['id' => 2, 'nombre' => 'María García'],
            ['id' => 3, 'nombre' => 'Carlos López'],
        ];
    }

    public function render()
    {
        return view('livewire.generar-reportes');
    }
}
