<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * Componente AprobacionesPendientes
 *
 * Muestra las requisiciones, traslados y compras que están pendientes de aprobación
 * por parte de usuarios con permisos administrativos.
 *
 * @package App\Livewire
 * @see resources/views/livewire/aprobaciones-pendientes.blade.php
 */
class AprobacionesPendientes extends Component
{
    /** @var array Listado de elementos pendientes de aprobación */
    public $pendientes = [];

    /** @var string Filtro por tipo (todos, requisiciones, traslados, compras) */
    public $filtroTipo = 'todos';

    /**
     * Inicializa el componente con datos de aprobaciones pendientes
     *
     * @todo Conectar con BD: obtener requisiciones, traslados y compras con estado "pendiente"
     * @return void
     */
    public function mount()
    {
        $this->cargarPendientes();
    }

    /**
     * Carga las aprobaciones pendientes desde la base de datos
     *
     * @return void
     */
    public function cargarPendientes()
    {
        // Mock data - Reemplazar con queries reales
        $this->pendientes = [
            [
                'id' => 1,
                'tipo' => 'requisicion',
                'numero' => 'REQ-2023-001',
                'solicitante' => 'Juan Pérez',
                'fecha' => '2023-10-26',
                'descripcion' => 'Requisición de materiales de oficina',
                'estado' => 'pendiente'
            ],
            [
                'id' => 2,
                'tipo' => 'traslado',
                'numero' => 'TRA-2023-015',
                'solicitante' => 'María García',
                'fecha' => '2023-10-25',
                'descripcion' => 'Traslado de herramientas a Bodega Central',
                'estado' => 'pendiente'
            ],
            [
                'id' => 3,
                'tipo' => 'compra',
                'numero' => 'COM-2023-045',
                'solicitante' => 'Carlos López',
                'fecha' => '2023-10-24',
                'descripcion' => 'Compra de equipos de protección',
                'estado' => 'pendiente'
            ],
        ];
    }

    /**
     * Filtra las aprobaciones por tipo
     *
     * @return array
     */
    public function getPendientesFiltradosProperty()
    {
        if ($this->filtroTipo === 'todos') {
            return $this->pendientes;
        }

        return array_filter($this->pendientes, function ($item) {
            return $item['tipo'] === $this->filtroTipo;
        });
    }

    /**
     * Aprueba un elemento pendiente
     *
     * @param int $id
     * @return void
     */
    public function aprobar($id)
    {
        // TODO: Implementar lógica de aprobación
        session()->flash('message', 'Elemento aprobado exitosamente.');
        $this->cargarPendientes();
    }

    /**
     * Rechaza un elemento pendiente
     *
     * @param int $id
     * @return void
     */
    public function rechazar($id)
    {
        // TODO: Implementar lógica de rechazo
        session()->flash('message', 'Elemento rechazado.');
        $this->cargarPendientes();
    }

    /**
     * Renderiza la vista del componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.aprobaciones-pendientes');
    }
}
