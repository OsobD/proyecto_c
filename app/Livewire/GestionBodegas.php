<?php

namespace App\Livewire;

use App\Models\Bodega;
use App\Models\Persona;
use App\Models\TarjetaResponsabilidad;
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

    /** @var string|null Nombre de la bodega (solo para tipo Física) */
    public $nombre;

    /** @var string|null Nombres de la persona (solo para tipo Responsabilidad) */
    public $nombres;

    /** @var string|null Apellidos de la persona (solo para tipo Responsabilidad) */
    public $apellidos;

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
     * Inicializa el componente cargando bodegas físicas y tarjetas de responsabilidad
     *
     * @return void
     */
    public function mount()
    {
        $this->cargarBodegas();
    }

    /**
     * Carga todas las bodegas (físicas y tarjetas de responsabilidad)
     *
     * @return void
     */
    private function cargarBodegas()
    {
        $this->bodegas = [];

        // Cargar bodegas físicas
        $bodegasFisicas = Bodega::all();
        foreach ($bodegasFisicas as $bodega) {
            $this->bodegas[] = [
                'id' => 'B-' . $bodega->id,
                'nombre' => $bodega->nombre,
                'tipo' => 'Física',
                'entidad' => 'bodega',
                'entidad_id' => $bodega->id,
            ];
        }

        // Cargar tarjetas de responsabilidad con sus personas
        $tarjetas = TarjetaResponsabilidad::with('persona')->get();
        foreach ($tarjetas as $tarjeta) {
            if ($tarjeta->persona) {
                $nombreCompleto = trim($tarjeta->persona->nombres . ' ' . $tarjeta->persona->apellidos);
                $this->bodegas[] = [
                    'id' => 'T-' . $tarjeta->id,
                    'nombre' => $nombreCompleto,
                    'tipo' => 'Responsabilidad',
                    'entidad' => 'tarjeta',
                    'entidad_id' => $tarjeta->id,
                ];
            }
        }
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
        $this->nombres = null;
        $this->apellidos = null;
        $this->tipo = null;
        $this->reset(['nombre', 'nombres', 'apellidos', 'tipo']);
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
     * @return void
     */
    public function saveBodega()
    {
        try {
            if ($this->tipo === 'Física') {
                // Validar campos para bodega física
                $this->validate([
                    'nombre' => 'required|string|max:255',
                ]);

                // Crear bodega física
                Bodega::create([
                    'nombre' => $this->nombre,
                ]);

                session()->flash('message', 'Bodega física creada exitosamente.');
            } elseif ($this->tipo === 'Responsabilidad') {
                // Validar campos para tarjeta de responsabilidad
                $this->validate([
                    'nombres' => 'required|string|max:255',
                    'apellidos' => 'required|string|max:255',
                ]);

                // Crear persona con solo nombres y apellidos
                $persona = Persona::create([
                    'nombres' => $this->nombres,
                    'apellidos' => $this->apellidos,
                    'estado' => true,
                ]);

                // Crear tarjeta de responsabilidad asociada a la persona
                TarjetaResponsabilidad::create([
                    'id_persona' => $persona->id,
                    'fecha_creacion' => now(),
                    'total' => 0,
                ]);

                session()->flash('message', 'Tarjeta de responsabilidad creada exitosamente.');
            } else {
                session()->flash('error', 'Debe seleccionar un tipo de bodega.');
                return;
            }

            // Recargar bodegas y cerrar modal
            $this->cargarBodegas();
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear: ' . $e->getMessage());
        }
    }
}
