<?php

namespace App\Livewire;

use Livewire\Component;

class BitacoraSistema extends Component
{
    public $logs = [];

    public function mount()
    {
        $this->logs = [
            [
                'fecha' => '2023-10-26 10:00:00',
                'usuario' => 'Juan Pérez',
                'accion' => 'Creación de Requisición',
                'descripcion' => 'Se creó la requisición #123 para 50 unidades de Tornillos de acero.'
            ],
            [
                'fecha' => '2023-10-26 09:30:00',
                'usuario' => 'admin@eemq.com',
                'accion' => 'Inicio de Sesión',
                'descripcion' => 'El usuario inició sesión exitosamente.'
            ],
            [
                'fecha' => '2023-10-25 15:00:00',
                'usuario' => 'María García',
                'accion' => 'Registro de Compra',
                'descripcion' => 'Se registró la compra #45 de 100 unidades de Cinta aislante.'
            ],
        ];
    }

    public function render()
    {
        return view('livewire.bitacora-sistema');
    }
}
