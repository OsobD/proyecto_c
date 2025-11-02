<?php

namespace Database\Seeders;

use App\Models\Bodega;
use App\Models\Entrada;
use App\Models\Lote;
use App\Models\TipoTransaccion;
use App\Models\Transaccion;
use Illuminate\Database\Seeder;

/**
 * Seeder para Lotes de Ajuste por Bodega
 *
 * Crea un lote especial de ajuste por cada bodega existente en el sistema.
 * Estos lotes se utilizan para registrar equipo no registrado que se devuelve
 * en buen estado.
 *
 * Características del lote de ajuste:
 * - Un lote por cada bodega activa
 * - id_producto = null (no está asociado a un producto específico)
 * - cantidad = 0 inicial (se incrementa con cada devolución)
 * - observaciones distintivas para identificación
 * - Vinculado a transacción tipo "Entrada"
 *
 * IMPORTANTE: Estos lotes se usan SOLO para equipo no registrado en buen estado.
 * El equipo dañado no se asigna a ningún lote.
 */
class LoteAjusteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener o crear tipo de transacción "Entrada"
        $tipoEntrada = TipoTransaccion::firstOrCreate(['nombre' => 'Entrada']);

        // Obtener todas las bodegas activas
        $bodegas = Bodega::where('activo', true)->get();

        if ($bodegas->isEmpty()) {
            $this->command->warn('⚠ No hay bodegas activas. No se crearon lotes de ajuste.');
            return;
        }

        foreach ($bodegas as $bodega) {
            // Verificar si ya existe un lote de ajuste para esta bodega
            $loteExistente = Lote::where('id_bodega', $bodega->id)
                ->where('observaciones', 'LIKE', '%Lote especial para equipo no registrado recuperado%')
                ->first();

            if ($loteExistente) {
                $this->command->info("✓ Lote de ajuste ya existe para bodega: {$bodega->nombre}");
                continue;
            }

            // Crear entrada para el lote
            $entrada = Entrada::create([
                'fecha' => now(),
                'total' => 0,
                'descripcion' => 'Lote de ajuste para equipo no registrado - ' . $bodega->nombre,
                'id_usuario' => null,
                'id_tarjeta' => null,
                'id_bodega' => $bodega->id,
            ]);

            // Crear transacción que referencia la entrada
            $transaccion = Transaccion::create([
                'id_tipo' => $tipoEntrada->id,
                'id_entrada' => $entrada->id,
            ]);

            // Crear lote de ajuste
            Lote::create([
                'cantidad' => 0,
                'cantidad_inicial' => 0,
                'fecha_ingreso' => now(),
                'precio_ingreso' => 0,
                'observaciones' => 'Lote especial para equipo no registrado recuperado',
                'id_producto' => null,
                'id_bodega' => $bodega->id,
                'estado' => true,
                'id_transaccion' => $transaccion->id,
            ]);

            $this->command->info("✓ Lote de ajuste creado para bodega: {$bodega->nombre}");
        }

        $this->command->info('✓ Lotes de ajuste creados exitosamente.');
    }
}
