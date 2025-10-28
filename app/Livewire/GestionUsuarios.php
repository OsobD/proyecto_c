<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * Componente GestionUsuarios
 *
 * Gestiona el listado de usuarios del sistema con su información básica (nombre, email, rol).
 *
 * @package App\Livewire
 * @see resources/views/livewire/gestion-usuarios.blade.php
 */
class GestionUsuarios extends Component
{
    /** @var array Listado de usuarios del sistema */
    public $usuarios = [];

    /**
     * Inicializa el componente con datos mock de prueba
     *
     * @todo Reemplazar con consultas a BD: User::all()
     * @return void
     */
    public function mount()
    {
        $this->usuarios = [
            ['id' => 1, 'nombre' => 'David Bautista', 'email' => 'admin@eemq.com', 'rol' => 'Administrador TI'],
            ['id' => 2, 'nombre' => 'Juan Pérez', 'email' => 'jperez@eemq.com', 'rol' => 'Colaborador'],
            ['id' => 3, 'nombre' => 'María García', 'email' => 'mgarcia@eemq.com', 'rol' => 'Jefe de Bodega'],
        ];
    }

    /**
     * Renderiza la vista del componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.gestion-usuarios');
    }
}
