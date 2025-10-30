<?php

namespace App\Livewire;

use App\Models\Bitacora;
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

    /** @var int|null ID de la bodega/tarjeta en edición */
    public $bodegaId = null;

    /** @var string|null Tipo de entidad ('bodega' o 'tarjeta') */
    public $entidadTipo = null;

    /** @var int|null ID de la persona asociada a una tarjeta */
    public $personaId = null;

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
     * Solo carga las que están activas
     *
     * @return void
     */
    private function cargarBodegas()
    {
        $this->bodegas = [];

        // Cargar solo bodegas físicas activas
        $bodegasFisicas = Bodega::where('activo', true)->get();
        foreach ($bodegasFisicas as $bodega) {
            $this->bodegas[] = [
                'id' => 'B-' . $bodega->id,
                'nombre' => $bodega->nombre,
                'tipo' => 'Física',
                'entidad' => 'bodega',
                'entidad_id' => $bodega->id,
            ];
        }

        // Cargar solo tarjetas de responsabilidad activas con sus personas
        $tarjetas = TarjetaResponsabilidad::where('activo', true)->with('persona')->get();
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
        $this->bodegaId = null;
        $this->entidadTipo = null;
        $this->personaId = null;
        $this->reset(['nombre', 'nombres', 'apellidos', 'tipo', 'bodegaId', 'entidadTipo', 'personaId']);
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
     * Carga los datos de una bodega/tarjeta para editar
     *
     * @param string $id ID compuesto (ej: 'B-1' o 'T-1')
     * @param string $tipo Tipo de bodega ('Física' o 'Responsabilidad')
     * @param int $entidadId ID real en la tabla
     * @param string $entidad Tipo de entidad ('bodega' o 'tarjeta')
     * @return void
     */
    public function editBodega($id, $tipo, $entidadId, $entidad)
    {
        $this->bodegaId = $entidadId;
        $this->entidadTipo = $entidad;
        $this->tipo = $tipo;

        if ($entidad === 'bodega') {
            // Cargar datos de bodega física
            $bodega = Bodega::find($entidadId);
            $this->nombre = $bodega->nombre;
        } else {
            // Cargar datos de tarjeta de responsabilidad
            $tarjeta = TarjetaResponsabilidad::with('persona')->find($entidadId);
            $this->personaId = $tarjeta->id_persona;
            $this->nombres = $tarjeta->persona->nombres;
            $this->apellidos = $tarjeta->persona->apellidos;
        }

        $this->isModalOpen = true;
        $this->showTipoDropdown = false;
    }

    /**
     * Guarda la bodega (crear o actualizar)
     *
     * @return void
     */
    public function saveBodega()
    {
        try {
            if ($this->bodegaId) {
                // MODO EDICIÓN
                if ($this->tipo === 'Física') {
                    // Validar y actualizar bodega física
                    $this->validate([
                        'nombre' => 'required|string|max:255',
                    ]);

                    $bodega = Bodega::find($this->bodegaId);
                    $bodega->update([
                        'nombre' => $this->nombre,
                    ]);

                    session()->flash('message', 'Bodega actualizada exitosamente.');
                } elseif ($this->tipo === 'Responsabilidad') {
                    // Validar y actualizar tarjeta de responsabilidad (persona)
                    $this->validate([
                        'nombres' => 'required|string|max:255',
                        'apellidos' => 'required|string|max:255',
                    ]);

                    $persona = Persona::find($this->personaId);
                    $persona->update([
                        'nombres' => $this->nombres,
                        'apellidos' => $this->apellidos,
                    ]);

                    session()->flash('message', 'Tarjeta de responsabilidad actualizada exitosamente.');
                }
            } else {
                // MODO CREACIÓN
                if ($this->tipo === 'Física') {
                    // Validar campos para bodega física
                    $this->validate([
                        'nombre' => 'required|string|max:255',
                    ]);

                    // Crear bodega física
                    Bodega::create([
                        'nombre' => $this->nombre,
                        'activo' => true,
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
                        'activo' => true,
                    ]);

                    session()->flash('message', 'Tarjeta de responsabilidad creada exitosamente.');
                } else {
                    session()->flash('error', 'Debe seleccionar un tipo de bodega.');
                    return;
                }
            }

            // Recargar bodegas y cerrar modal
            $this->cargarBodegas();
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Activa o desactiva una bodega/tarjeta (soft delete)
     *
     * @param int $entidadId ID de la entidad
     * @param string $entidad Tipo de entidad ('bodega' o 'tarjeta')
     * @return void
     */
    public function toggleEstado($entidadId, $entidad)
    {
        try {
            if ($entidad === 'bodega') {
                $bodega = Bodega::find($entidadId);

                if (!$bodega) {
                    session()->flash('error', 'Bodega no encontrada.');
                    return;
                }

                // Verificar si tiene relaciones activas antes de desactivar
                if ($bodega->activo) {
                    if ($bodega->lotes()->count() > 0 ||
                        $bodega->compras()->count() > 0 ||
                        $bodega->entradas()->count() > 0 ||
                        $bodega->devoluciones()->count() > 0 ||
                        $bodega->traslados()->count() > 0 ||
                        $bodega->salidas()->count() > 0) {
                        session()->flash('error', 'No se puede desactivar. La bodega tiene movimientos asociados.');
                        return;
                    }
                }

                // Cambiar estado
                $nuevoEstado = !$bodega->activo;
                $bodega->activo = $nuevoEstado;
                $bodega->save();

                // Registrar en bitácora
                Bitacora::create([
                    'accion' => $nuevoEstado ? 'activar' : 'desactivar',
                    'modelo' => 'Bodega',
                    'modelo_id' => $entidadId,
                    'descripcion' => auth()->user()->name . ($nuevoEstado ? ' activó ' : ' desactivó ') . "Bodega '{$bodega->nombre}' (#{$entidadId})",
                    'datos_anteriores' => json_encode(['activo' => !$nuevoEstado]),
                    'datos_nuevos' => json_encode(['activo' => $nuevoEstado]),
                    'id_usuario' => auth()->id(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_at' => now(),
                ]);

                session()->flash('message', $nuevoEstado ? 'Bodega activada exitosamente.' : 'Bodega desactivada exitosamente.');
            } else {
                // Tarjeta de responsabilidad
                $tarjeta = TarjetaResponsabilidad::with('persona')->find($entidadId);

                if (!$tarjeta) {
                    session()->flash('error', 'Tarjeta de responsabilidad no encontrada.');
                    return;
                }

                // Verificar si tiene relaciones activas antes de desactivar
                if ($tarjeta->activo) {
                    if ($tarjeta->tarjetasProducto()->count() > 0 ||
                        $tarjeta->entradas()->count() > 0) {
                        session()->flash('error', 'No se puede desactivar. La tarjeta tiene productos o movimientos asociados.');
                        return;
                    }
                }

                // Cambiar estado
                $nuevoEstado = !$tarjeta->activo;
                $tarjeta->activo = $nuevoEstado;
                $tarjeta->save();

                // Registrar en bitácora
                $nombrePersona = $tarjeta->persona ? trim($tarjeta->persona->nombres . ' ' . $tarjeta->persona->apellidos) : 'Sin persona';
                Bitacora::create([
                    'accion' => $nuevoEstado ? 'activar' : 'desactivar',
                    'modelo' => 'TarjetaResponsabilidad',
                    'modelo_id' => $entidadId,
                    'descripcion' => auth()->user()->name . ($nuevoEstado ? ' activó ' : ' desactivó ') . "Tarjeta de Responsabilidad de '{$nombrePersona}' (#{$entidadId})",
                    'datos_anteriores' => json_encode(['activo' => !$nuevoEstado]),
                    'datos_nuevos' => json_encode(['activo' => $nuevoEstado]),
                    'id_usuario' => auth()->id(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_at' => now(),
                ]);

                session()->flash('message', $nuevoEstado ? 'Tarjeta activada exitosamente.' : 'Tarjeta desactivada exitosamente.');
            }

            // Recargar bodegas
            $this->cargarBodegas();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cambiar estado: ' . $e->getMessage());
        }
    }
}
