<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * @class GestionUsuarios
 * @package App\Livewire
 * @brief Componente para la gestión de usuarios del sistema.
 *
 * Este componente se encarga de mostrar una lista de los usuarios registrados.
 * Actualmente, utiliza datos de ejemplo para la visualización.
 */
class GestionUsuarios extends Component
{
    /**
     * @var array
     * @brief Almacena la lista de usuarios.
     *
     * Cada usuario es un array asociativo con 'id', 'nombre', 'email' y 'rol'.
     */
    public $usuarios = [];

    /**
     * @brief Método que se ejecuta al inicializar el componente.
     *
     * Carga los datos de ejemplo de usuarios. En una implementación futura,
     * este método debería obtener los datos desde la base de datos.
     *
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
     * @brief Renderiza la vista del componente.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.gestion-usuarios');
    }
}
