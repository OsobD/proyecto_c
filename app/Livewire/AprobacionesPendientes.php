<?php

namespace App\Livewire;

use App\Models\CambioPendiente;
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
        // Cargar cambios pendientes de la base de datos
        $cambiosPendientes = CambioPendiente::with(['usuarioSolicitante', 'usuarioAprobador'])
            ->pendientes()
            ->orderBy('created_at', 'desc')
            ->get();

        $this->pendientes = $cambiosPendientes->map(function ($cambio) {
            // Determinar el tipo de acción y descripción
            $tipoDescripcion = $this->obtenerDescripcionCambio($cambio);

            return [
                'id' => $cambio->id,
                'tipo' => strtolower($cambio->accion),
                'modelo' => $cambio->modelo,
                'numero' => $tipoDescripcion['numero'],
                'solicitante' => $cambio->usuarioSolicitante ? $cambio->usuarioSolicitante->name : 'Desconocido',
                'fecha' => $cambio->created_at->format('Y-m-d'),
                'fecha_completa' => $cambio->created_at->format('d/m/Y H:i'),
                'descripcion' => $tipoDescripcion['descripcion'],
                'justificacion' => $cambio->justificacion,
                'estado' => $cambio->estado,
                'accion' => ucfirst($cambio->accion),
            ];
        })->toArray();
    }

    /**
     * Obtiene la descripción del cambio según el modelo y acción
     *
     * @param CambioPendiente $cambio
     * @return array
     */
    private function obtenerDescripcionCambio($cambio)
    {
        $numero = '';
        $descripcion = '';

        switch ($cambio->modelo) {
            case 'Salida':
                $numero = 'REQ-' . $cambio->modelo_id;
                $descripcion = "Solicitud de {$cambio->accion} de Requisición (Salida)";
                break;

            case 'Traslado':
                $numero = 'TRA-' . $cambio->modelo_id;
                $descripcion = "Solicitud de {$cambio->accion} de Traslado/Requisición";
                break;

            case 'Compra':
                $numero = 'COM-' . $cambio->modelo_id;
                $descripcion = "Solicitud de {$cambio->accion} de Compra";
                break;

            default:
                $numero = $cambio->modelo . '-' . $cambio->modelo_id;
                $descripcion = "Solicitud de {$cambio->accion} de {$cambio->modelo}";
                break;
        }

        return [
            'numero' => $numero,
            'descripcion' => $descripcion,
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
        try {
            $cambio = CambioPendiente::findOrFail($id);
            $usuario = auth()->user();

            if (!$usuario) {
                session()->flash('error', 'Debe iniciar sesión.');
                return;
            }

            // Aprobar el cambio (esto aplicará automáticamente el cambio según el modelo)
            $cambio->aprobar($usuario->id, 'Aprobado por el administrador');

            session()->flash('success', 'Solicitud aprobada y aplicada exitosamente.');
            $this->cargarPendientes();

        } catch (\Exception $e) {
            \Log::error('Error al aprobar cambio', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'Error al aprobar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Rechaza un elemento pendiente
     *
     * @param int $id
     * @return void
     */
    public function rechazar($id)
    {
        try {
            $cambio = CambioPendiente::findOrFail($id);
            $usuario = auth()->user();

            if (!$usuario) {
                session()->flash('error', 'Debe iniciar sesión.');
                return;
            }

            // Rechazar el cambio
            $cambio->rechazar($usuario->id, 'Rechazado por el administrador');

            session()->flash('success', 'Solicitud rechazada exitosamente.');
            $this->cargarPendientes();

        } catch (\Exception $e) {
            \Log::error('Error al rechazar cambio', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'Error al rechazar la solicitud: ' . $e->getMessage());
        }
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
