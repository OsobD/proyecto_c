<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * Componente GestionBodegas
 *
 * Gestiona el CRUD de bodegas del sistema de inventario. Permite administrar tanto
 * bodegas físicas como tarjetas de responsabilidad (bodegas virtuales asignadas a personas).
 *
 * **Tipos de bodegas:**
 * - Física: Almacenes o bodegas físicas de la organización
 * - Responsabilidad: Tarjetas de responsabilidad asignadas a personas específicas
 *
 * @package App\Livewire
 * @see resources/views/livewire/gestion-bodegas.blade.php
 */
class GestionBodegas extends Component
{
    /** @var array Listado de bodegas */
    public $bodegas = [];

    /** @var bool Controla visibilidad del modal */
    public $isModalOpen = false;

    /** @var string|null Nombre de la bodega */
    public $nombre;

    /** @var string|null Tipo de bodega seleccionado */
    public $tipo = null;

    /** @var bool Controla visibilidad del dropdown de tipo */
    public $showTipoDropdown = false;

    /** @var array Tipos de bodega disponibles */
    public $tiposDisponibles = [
        ['id' => 1, 'nombre' => 'Física'],
        ['id' => 2, 'nombre' => 'Responsabilidad'],
    ];

    /**
     * Inicializa el componente con datos mock de prueba
     *
     * @todo Reemplazar con consultas a BD: Bodega::all()
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

    /**
     * Renderiza la vista del componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.gestion-bodegas');
    }

    /**
     * Abre el modal para crear/editar bodega
     *
     * @return void
     */
    public function openModal()
    {
        $this->isModalOpen = true;
        $this->showTipoDropdown = false;
    }

    /**
     * Cierra el modal y limpia el formulario
     *
     * @return void
     */
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->nombre = null;
        $this->tipo = null;
    }

    /**
     * Selecciona un tipo de bodega desde el dropdown
     *
     * @param string $tipo Tipo de bodega seleccionado
     * @return void
     */
    public function selectTipo($tipo)
    {
        $this->tipo = $tipo;
        $this->showTipoDropdown = false;
    }

    /**
     * Limpia la selección de tipo de bodega
     *
     * @return void
     */
    public function clearTipo()
    {
        $this->tipo = null;
    }

    /**
     * Guarda la bodega (crear o actualizar)
     *
     * @todo Implementar validación y persistencia en BD
     * @return void
     */
    public function saveBodega()
    {
        $this->closeModal();
    }
}
