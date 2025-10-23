<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * @class GestionProveedores
 * @package App\Livewire
 * @brief Componente para la gestión de proveedores.
 *
 * Este componente se encarga de mostrar una lista de los proveedores
 * registrados en el sistema. Actualmente, utiliza datos de ejemplo.
 */
class GestionProveedores extends Component
{
    /**
     * @var array
     * @brief Almacena la lista de proveedores.
     *
     * Cada proveedor es un array asociativo con 'id', 'nombre', 'contacto' y 'estado'.
     */
    public $proveedores = [];

    /**
     * @brief Método que se ejecuta al inicializar el componente.
     *
     * Carga los datos de ejemplo en la propiedad $proveedores. En una implementación
     * futura, este método debería obtener los datos desde la base de datos.
     *
     * @return void
     */
    public function mount()
    {
        $this->proveedores = [
            ['id' => 1, 'nombre' => 'Ferretería El Martillo Feliz', 'contacto' => 'contacto@martillo.com', 'estado' => 'Activo'],
            ['id' => 2, 'nombre' => 'Suministros Industriales S.A.', 'contacto' => 'ventas@suministros.com', 'estado' => 'Activo'],
            ['id' => 3, 'nombre' => 'Oficina Total', 'contacto' => 'info@ofitotal.com', 'estado' => 'Inactivo'],
        ];
    }

    /**
     * @brief Renderiza la vista del componente.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.gestion-proveedores');
    }
}
