<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Implanta datos en la base de datos para pruebas
     *
     * ORDEN DE EJECUCIÓN:
     * 1. Roles, Permisos y Usuarios (seguridad)
     * 2. Catálogos base (categorías, bodegas, regímenes)
     * 3. Tipos de transacción (crítico para sistema de lotes)
     * 4. Tipos de entrada y salida (subtipos)
     * 5. Lotes especiales (lotes de ajuste)
     * 6. Proveedores de prueba (opcional)
     */
    public function run(): void
    {
        // 1. Seguridad y Usuarios
        $this->call([
            RolesPermisosSeeder::class,
            PuestoSeeder::class,
            UsuarioAdminSeeder::class,
        ]);

        // 2. Catálogos Base
        $this->call([
            CategoriaSeeder::class,        // Requerido para crear productos
            BodegaSeeder::class,           // Requerido para compras y movimientos
            RegimenTributarioSeeder::class, // Requerido para crear proveedores
        ]);

        // 3. Tipos de Transacción
        $this->call([
            TipoTransaccionSeeder::class,  // Compra, Entrada, Devolución, Traslado, Salida
        ]);

        // 4. Subtipos de Entrada y Salida
        $this->call([
            TipoEntradaSeeder::class,      // Donación, Ajuste, Producción, etc.
            TipoSalidaSeeder::class,       // Venta, Uso Interno, Merma, etc.
        ]);

        // 5. Lotes Especiales
        $this->call([
            LoteAjusteSeeder::class,       // Lotes de ajuste por bodega (para equipo no registrado)
        ]);

        // 6. Datos de Prueba (OPCIONAL - comentar en producción)
        $this->call([
            ProveedorSeeder::class,        // Proveedores de ejemplo
            PersonasTestSeeder::class,     // 20 personas para probar paginación
        ]);

        $this->command->info('');
        $this->command->info('==================================================');
        $this->command->info('✓ TODOS LOS SEEDERS EJECUTADOS EXITOSAMENTE');
        $this->command->info('==================================================');
        $this->command->info('Sistema listo para usar con:');
        $this->command->info('  • 5 Tipos de Transacción');
        $this->command->info('  • 3 Bodegas activas');
        $this->command->info('  • 6 Categorías de productos');
        $this->command->info('  • 3 Regímenes tributarios');
        $this->command->info('  • 4 Tipos de entrada');
        $this->command->info('  • 6 Tipos de salida');
        $this->command->info('  • Lotes de ajuste por bodega');
        $this->command->info('  • 2 Proveedores de prueba');
        $this->command->info('  • 20 Personas de prueba');
        $this->command->info('==================================================');
    }
}
