<?php

namespace App\Livewire;

use Livewire\Component;

class FormularioRequisicion extends Component
{
    public $empleados = [];
    public $productos = [];

    public function mount()
    {
        $this->empleados = [
            ['id' => 1, 'nombre' => 'Juan Pérez'],
            ['id' => 2, 'nombre' => 'María García'],
            ['id' => 3, 'nombre' => 'Carlos López'],
        ];

        $this->productos = [
            ['codigo' => 'PROD-001', 'descripcion' => 'Tornillos de acero inoxidable'],
            ['codigo' => 'PROD-002', 'descripcion' => 'Abrazaderas de metal'],
            ['codigo' => 'PROD-003', 'descripcion' => 'Cinta aislante'],
            ['codigo' => 'PROD-004', 'descripcion' => 'Guantes de seguridad'],
            ['codigo' => 'PROD-005', 'descripcion' => 'Fusibles de 15A'],
        ];
    }

    public function render()
    {
        return view('livewire.formulario-requisicion');
    }
}
