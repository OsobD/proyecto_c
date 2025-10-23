<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * @class GestionBodegas
 * @package App\Livewire
 * @brief Componente para la gestión de bodegas y tarjetas de responsabilidad.
 *
 * Este componente permite a los usuarios ver una lista de las bodegas existentes
 * (tanto físicas como de responsabilidad) y agregar nuevas a través de un modal.
 */
class GestionBodegas extends Component
{
    // --- PROPIEDADES PÚBLICAS ---

    /** @var array Lista de bodegas a mostrar. */
    public $bodegas = [];
    /** @var bool Controla la visibilidad del modal de creación/edición. */
    public $isModalOpen = false;
    /** @var string Nombre de la nueva bodega. */
    public $nombre;
    /** @var string|null Tipo de la nueva bodega ('Física' o 'Responsabilidad'). */
    public $tipo = null;
    /** @var bool Controla la visibilidad del dropdown para seleccionar el tipo. */
    public $showTipoDropdown = false;

    /** @var array Lista de los tipos de bodega disponibles para la selección. */
    public $tiposDisponibles = [
        ['id' => 1, 'nombre' => 'Física'],
        ['id' => 2, 'nombre' => 'Responsabilidad'],
    ];

    // --- MÉTODOS DE CICLO DE VIDA ---

    /**
     * @brief Método que se ejecuta al inicializar el componente.
     * Carga datos de ejemplo para la lista de bodegas.
     * @return void
     */
    public function mount()
    {
        $this->bodegas = [
            ['id' => 1, 'nombre' => 'Bodega Central', 'tipo' => 'Física'],
            ['id' => 2, 'nombre' => 'Juan Pérez (Tarjeta de Responsabilidad)', 'tipo' => 'Responsabilidad'],
            ['id' => 3, 'nombre' => 'Almacén de Suministros', 'tipo' => 'Física'],
            ['id' => 4, 'nombre' => 'María García (Tarjeta de Responsabilidad)', 'tipo' => 'Responsabilidad'],
            ['id' => 5, 'nombre' => 'Bodega Secundaria', 'tipo' => 'Física'],
        ];
    }

    // --- MÉTODOS DE MANEJO DEL MODAL ---

    /**
     * @brief Abre el modal para crear una nueva bodega.
     * @return void
     */
    public function openModal()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    /**
     * @brief Cierra el modal y reinicia el formulario.
     * @return void
     */
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    /**
     * @brief Reinicia las propiedades del formulario del modal.
     * @return void
     */
    private function resetForm()
    {
        $this->nombre = '';
        $this->tipo = null;
        $this->showTipoDropdown = false;
    }

    /**
     * @brief Simula el guardado de una nueva bodega.
     * En una implementación real, aquí iría la lógica de validación y
     * almacenamiento en la base de datos.
     * @return void
     */
    public function saveBodega()
    {
        // Lógica de validación y guardado iría aquí.
        $this->closeModal();
    }

    // --- MÉTODOS DE SELECCIÓN ---

    /**
     * @brief Establece el tipo de bodega seleccionado desde el dropdown.
     * @param string $tipo El tipo de bodega a seleccionar.
     * @return void
     */
    public function selectTipo($tipo)
    {
        $this->tipo = $tipo;
        $this->showTipoDropdown = false;
    }

    /**
     * @brief Limpia la selección de tipo de bodega.
     * @return void
     */
    public function clearTipo()
    {
        $this->tipo = null;
    }

    /**
     * @brief Renderiza la vista del componente.
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.gestion-bodegas');
    }
}
