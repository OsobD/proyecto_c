<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use App\Models\RegimenTributario;
use Illuminate\Database\Seeder;

/**
 * Seeder para Proveedores de Prueba
 *
 * Crea proveedores de ejemplo para facilitar el testing y desarrollo
 * del sistema. Este seeder es OPCIONAL y puede comentarse en producción.
 *
 * IMPORTANTE: Este seeder debe ejecutarse DESPUÉS de RegimenTributarioSeeder
 * ya que requiere que existan regímenes tributarios.
 */
class ProveedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener regímenes tributarios
        $regimenGeneral = RegimenTributario::where('nombre', 'Régimen General')->first();
        $pequenoContribuyente = RegimenTributario::where('nombre', 'Pequeño Contribuyente')->first();

        // Validar que existan regímenes
        if (!$regimenGeneral || !$pequenoContribuyente) {
            $this->command->error('❌ Error: No se encontraron regímenes tributarios. Ejecute RegimenTributarioSeeder primero.');
            return;
        }

        $proveedores = [
            [
                'nit' => '12345678-9',
                'id_regimen' => $regimenGeneral->id,
                'nombre' => 'Distribuidora La Económica S.A.',
                'activo' => true,
            ],
            [
                'nit' => '98765432-1',
                'id_regimen' => $pequenoContribuyente->id,
                'nombre' => 'Papelería El Estudiante',
                'activo' => true,
            ],
        ];

        foreach ($proveedores as $proveedor) {
            Proveedor::firstOrCreate(
                ['nit' => $proveedor['nit']],
                $proveedor
            );
        }

        $this->command->info('✓ Proveedores de prueba creados exitosamente.');
    }
}
