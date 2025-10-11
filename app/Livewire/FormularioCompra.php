<?php

namespace App\Livewire;

use Livewire\Component;

class FormularioCompra extends Component
{
    public $proveedores = [];
    public $productos = [];

    public function mount()
    {
        $this->proveedores = [
            ['id' => 1, 'nombre' => 'Ferretería El Martillo Feliz'],
            ['id' => 2, 'nombre' => 'Suministros Industriales S.A.'],
        ];

        $this->productos = [
            ['codigo' => 'PROD-001', 'descripcion' => 'Tornillos de acero inoxidable'],
            ['codigo' => 'PROD-002', 'descripcion' => 'Abrazaderas de metal'],
            ['codigo' => 'PROD-003', 'descripcion' => 'Cinta aislante'],
        ];
    }

    public function render()
    {
        return view('livewire.formulario-compra');
    }
}
