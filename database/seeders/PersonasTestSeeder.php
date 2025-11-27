<?php

namespace Database\Seeders;

use App\Models\Persona;
use App\Models\Puesto;
use Illuminate\Database\Seeder;

/**
 * Seeder de Personas de Prueba
 *
 * Crea 20 personas de ejemplo para facilitar testing de paginación
 * y funcionalidades del sistema.
 *
 * IMPORTANTE: Este seeder es OPCIONAL y solo debe usarse en desarrollo.
 */
class PersonasTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener un puesto por defecto
        $puesto = Puesto::first();

        if (!$puesto) {
            $this->command->warn('⚠️  Advertencia: No se encontró ningún puesto. Las personas se crearán sin puesto asignado.');
        }

        $nombres = ['Juan', 'María', 'Carlos', 'Ana', 'Luis', 'Carmen', 'José', 'Laura', 'Miguel', 'Elena'];
        $apellidos = ['García', 'Rodríguez', 'Martínez', 'López', 'González', 'Pérez', 'Sánchez', 'Ramírez', 'Torres', 'Flores'];

        $personas = [];

        for ($i = 1; $i <= 20; $i++) {
            $nombre = $nombres[array_rand($nombres)];
            $apellido1 = $apellidos[array_rand($apellidos)];
            $apellido2 = $apellidos[array_rand($apellidos)];

            $personas[] = [
                'nombres' => $nombre . ' ' . chr(64 + ($i % 26)),
                'apellidos' => $apellido1 . ' ' . $apellido2,
                'correo' => strtolower($nombre . '.' . $apellido1 . $i . '@eemq.com'),
                'telefono' => '2' . str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                'dpi' => str_pad(rand(1000000000000, 9999999999999), 13, '0', STR_PAD_LEFT),
                'id_puesto' => $puesto ? $puesto->id : null,
                'activo' => $i <= 18, // 18 activos, 2 inactivos para probar filtros
                'created_at' => now()->subDays(rand(1, 90)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ];
        }

        foreach ($personas as $persona) {
            Persona::firstOrCreate(
                ['dpi' => $persona['dpi']],
                $persona
            );
        }

        $this->command->info('✓ 20 personas de prueba creadas exitosamente.');
    }
}
