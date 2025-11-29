<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Migra datos de cambio_pendiente a solicitud_aprobacion
     * para unificar el sistema de aprobaciones
     */
    public function up(): void
    {
        // Verificar si la tabla cambio_pendiente existe
        // Esto evita errores en migrate:fresh donde la tabla no existe aún
        if (!Schema::hasTable('cambio_pendiente')) {
            \Log::info('Tabla cambio_pendiente no existe, saltando migración de datos');
            return;
        }

        // Obtener todos los registros de cambio_pendiente
        $cambiosPendientes = DB::table('cambio_pendiente')->get();

        if ($cambiosPendientes->isEmpty()) {
            \Log::info('No hay registros en cambio_pendiente para migrar');
            return;
        }

        foreach ($cambiosPendientes as $cambio) {
            // Mapear modelo a nombre de tabla
            $tabla = $this->modeloToTabla($cambio->modelo);

            // Mapear acción a tipo (convertir a mayúsculas)
            $tipo = strtoupper($cambio->accion);

            // Mapear estado (convertir a mayúsculas)
            $estado = strtoupper($cambio->estado);

            // Insertar en solicitud_aprobacion
            DB::table('solicitud_aprobacion')->insert([
                'tipo' => $tipo,
                'tabla' => $tabla,
                'registro_id' => $cambio->modelo_id,
                'datos' => $cambio->datos_nuevos, // Usar datos_nuevos como datos principales
                'solicitante_id' => $cambio->usuario_solicitante_id,
                'aprobador_id' => $cambio->usuario_aprobador_id,
                'estado' => $estado,
                'observaciones' => $cambio->justificacion ?? '',
                'created_at' => $cambio->created_at,
                'updated_at' => $cambio->updated_at,
            ]);
        }

        // Log de migración
        \Log::info('Migración de cambio_pendiente a solicitud_aprobacion completada', [
            'registros_migrados' => $cambiosPendientes->count()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar los registros migrados de solicitud_aprobacion
        // Solo eliminar los que fueron migrados (no los creados directamente)
        // Para seguridad, no eliminamos nada en el down
        // Si necesitas revertir, hazlo manualmente

        \Log::warning('Revertir migración de solicitud_aprobacion no está implementado por seguridad');
    }

    /**
     * Mapea el nombre del modelo al nombre de la tabla
     *
     * @param string $modelo
     * @return string
     */
    private function modeloToTabla(string $modelo): string
    {
        $map = [
            'Salida' => 'salida',
            'Traslado' => 'traslado',
            'Devolucion' => 'devolucion',
            'Entrada' => 'entrada',
            'Compra' => 'compra',
            'Categoria' => 'categoria',
            'Proveedor' => 'proveedor',
            'Persona' => 'persona',
            'Bodega' => 'bodega',
            'TarjetaResponsabilidad' => 'tarjeta_responsabilidad',
            'Producto' => 'producto',
            'Usuario' => 'usuario',
        ];

        return $map[$modelo] ?? strtolower($modelo);
    }
};
