<?php

namespace App\Traits;

use App\Models\Bitacora;

trait Auditable
{
    /**
     * Boot del trait para registrar eventos
     */
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::registrarEnBitacora('crear', $model);
        });

        static::updated(function ($model) {
            self::registrarEnBitacora('editar', $model);
        });

        static::deleted(function ($model) {
            self::registrarEnBitacora('eliminar', $model);
        });
    }

    /**
     * Registra una acción en la bitácora
     */
    protected static function registrarEnBitacora($accion, $model)
    {
        // Solo registrar si hay un usuario autenticado
        if (!auth()->check()) {
            return;
        }

        try {
            Bitacora::create([
                'accion' => $accion,
                'modelo' => class_basename($model),
                'modelo_id' => $model->id,
                'descripcion' => self::generarDescripcion($accion, $model),
                'datos_anteriores' => $accion === 'editar' ? json_encode($model->getOriginal()) : null,
                'datos_nuevos' => json_encode($model->getAttributes()),
                'id_usuario' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Log del error pero no detener la ejecución
            \Log::error('Error al registrar en bitácora: ' . $e->getMessage());
        }
    }

    /**
     * Genera una descripción legible de la acción
     */
    protected static function generarDescripcion($accion, $model)
    {
        $nombreModelo = class_basename($model);
        $usuario = auth()->user()->name ?? 'Usuario';

        // Intentar obtener un identificador más descriptivo del modelo
        $identificador = $model->id;
        if (property_exists($model, 'nombre') && isset($model->nombre)) {
            $identificador = "'{$model->nombre}' (#{$model->id})";
        } elseif (property_exists($model, 'nombres') && isset($model->nombres)) {
            $apellidos = $model->apellidos ?? '';
            $identificador = "'{$model->nombres} {$apellidos}' (#{$model->id})";
        }

        switch ($accion) {
            case 'crear':
                return "{$usuario} creó {$nombreModelo} {$identificador}";
            case 'editar':
                return "{$usuario} editó {$nombreModelo} {$identificador}";
            case 'eliminar':
                return "{$usuario} eliminó {$nombreModelo} {$identificador}";
            default:
                return "{$usuario} realizó acción '{$accion}' en {$nombreModelo} {$identificador}";
        }
    }
}
