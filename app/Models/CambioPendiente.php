<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CambioPendiente extends Model
{
    use HasFactory;

    protected $table = 'cambios_pendientes';

    protected $fillable = [
        'modelo',
        'modelo_id',
        'accion',
        'datos_anteriores',
        'datos_nuevos',
        'usuario_solicitante_id',
        'estado',
        'usuario_aprobador_id',
        'fecha_aprobacion',
        'justificacion',
        'observaciones',
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
        'fecha_aprobacion' => 'datetime',
    ];

    // Relaciones
    public function usuarioSolicitante()
    {
        return $this->belongsTo(Usuario::class, 'usuario_solicitante_id');
    }

    public function usuarioAprobador()
    {
        return $this->belongsTo(Usuario::class, 'usuario_aprobador_id');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAprobados($query)
    {
        return $query->where('estado', 'aprobado');
    }

    public function scopeRechazados($query)
    {
        return $query->where('estado', 'rechazado');
    }

    public function scopeDelModulo($query, $modulo)
    {
        return $query->where('modelo', $modulo);
    }

    /**
     * Aprobar el cambio y aplicarlo
     */
    public function aprobar($usuarioAprobadorId, $observaciones = null)
    {
        $this->estado = 'aprobado';
        $this->usuario_aprobador_id = $usuarioAprobadorId;
        $this->fecha_aprobacion = now();
        $this->observaciones = $observaciones;
        $this->save();

        // Aplicar el cambio según la acción
        $this->aplicarCambio();

        return true;
    }

    /**
     * Rechazar el cambio
     */
    public function rechazar($usuarioAprobadorId, $observaciones = null)
    {
        $this->estado = 'rechazado';
        $this->usuario_aprobador_id = $usuarioAprobadorId;
        $this->fecha_aprobacion = now();
        $this->observaciones = $observaciones;
        $this->save();

        return true;
    }

    /**
     * Aplicar el cambio al modelo original
     */
    protected function aplicarCambio()
    {
        $modeloClass = "App\\Models\\{$this->modelo}";

        if (!class_exists($modeloClass)) {
            throw new \Exception("Modelo {$this->modelo} no existe");
        }

        switch ($this->accion) {
            case 'crear':
                // Crear nuevo registro
                $modeloClass::create($this->datos_nuevos);
                break;

            case 'editar':
                // Actualizar registro existente
                $registro = $modeloClass::find($this->modelo_id);
                if ($registro) {
                    $registro->update($this->datos_nuevos);
                }
                break;

            case 'eliminar':
                // Eliminar/desactivar registro
                $registro = $modeloClass::find($this->modelo_id);
                if ($registro) {
                    // Si tiene campo 'activo', desactivar en lugar de eliminar
                    if (in_array('activo', $registro->getFillable())) {
                        $registro->update(['activo' => false]);
                    } else {
                        $registro->delete();
                    }
                }
                break;
        }

        // Registrar en bitácora
        Bitacora::create([
            'accion' => ucfirst($this->accion),
            'modelo' => $this->modelo,
            'modelo_id' => $this->modelo_id,
            'descripcion' => "Cambio aprobado: {$this->accion} en {$this->modelo}",
            'id_usuario' => $this->usuario_aprobador_id,
            'created_at' => now(),
        ]);
    }

    /**
     * Obtener diferencias entre datos anteriores y nuevos
     */
    public function getDiferencias()
    {
        $diferencias = [];

        foreach ($this->datos_nuevos as $campo => $valorNuevo) {
            $valorAnterior = $this->datos_anteriores[$campo] ?? null;

            if ($valorAnterior != $valorNuevo) {
                $diferencias[$campo] = [
                    'anterior' => $valorAnterior,
                    'nuevo' => $valorNuevo,
                ];
            }
        }

        return $diferencias;
    }

    /**
     * Obtener badge de estado
     */
    public function getEstadoBadgeAttribute()
    {
        return match($this->estado) {
            'pendiente' => '<span class="bg-yellow-200 text-yellow-800 py-1 px-3 rounded-full text-xs font-semibold">Pendiente</span>',
            'aprobado' => '<span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs font-semibold">Aprobado</span>',
            'rechazado' => '<span class="bg-red-200 text-red-800 py-1 px-3 rounded-full text-xs font-semibold">Rechazado</span>',
            default => '<span class="bg-gray-200 text-gray-800 py-1 px-3 rounded-full text-xs font-semibold">Desconocido</span>',
        };
    }

    /**
     * Obtener badge de acción
     */
    public function getAccionBadgeAttribute()
    {
        return match($this->accion) {
            'crear' => '<span class="bg-blue-200 text-blue-800 py-1 px-3 rounded-full text-xs font-semibold">Crear</span>',
            'editar' => '<span class="bg-amber-200 text-amber-800 py-1 px-3 rounded-full text-xs font-semibold">Editar</span>',
            'eliminar' => '<span class="bg-red-200 text-red-800 py-1 px-3 rounded-full text-xs font-semibold">Eliminar</span>',
            default => '<span class="bg-gray-200 text-gray-800 py-1 px-3 rounded-full text-xs font-semibold">Desconocido</span>',
        };
    }
}
