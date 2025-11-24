<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Support\Facades\DB;

class AsignarRolAdminSeeder extends Seeder
{
    /**
     * Asignar rol de Administrador TI al primer usuario del sistema
     *
     * Uso: php artisan db:seed --class=AsignarRolAdminSeeder
     */
    public function run(): void
    {
        $this->command->info('🔍 Buscando rol de Administrador TI...');

        // Buscar el rol de Administrador TI
        $rolAdmin = Rol::where('nombre', 'Administrador TI')->first();

        if (!$rolAdmin) {
            $this->command->error('❌ No se encontró el rol "Administrador TI"');
            $this->command->warn('   Ejecuta primero: php artisan db:seed --class=RolesPermisosSeeder');
            return;
        }

        $this->command->info("✅ Rol encontrado (ID: {$rolAdmin->id})");
        $this->command->newLine();

        // Mostrar usuarios disponibles
        $this->command->info('👥 Usuarios disponibles:');
        $usuarios = Usuario::with('persona')->get();

        if ($usuarios->isEmpty()) {
            $this->command->error('❌ No hay usuarios en el sistema');
            return;
        }

        foreach ($usuarios as $usuario) {
            $nombreCompleto = $usuario->persona
                ? "{$usuario->persona->nombres} {$usuario->persona->apellidos}"
                : 'Sin persona';

            $rolActual = $usuario->rol ? $usuario->rol->nombre : 'Sin rol';

            $this->command->line("   [{$usuario->id}] {$usuario->nombre_usuario} - {$nombreCompleto} (Rol actual: {$rolActual})");
        }

        $this->command->newLine();

        // Asignar al primer usuario por defecto
        $primerUsuario = $usuarios->first();

        $this->command->warn("⚙️  Asignando rol de Administrador TI a:");
        $this->command->line("   Usuario ID: {$primerUsuario->id}");
        $this->command->line("   Username: {$primerUsuario->nombre_usuario}");

        $primerUsuario->id_rol = $rolAdmin->id;
        $primerUsuario->save();

        $this->command->newLine();
        $this->command->info('✅ ¡Rol asignado exitosamente!');
        $this->command->newLine();

        $this->command->line('──────────────────────────────────────────────────────');
        $this->command->info('🎯 Próximos pasos:');
        $this->command->line('   1. Cierra sesión y vuelve a iniciar sesión');
        $this->command->line('   2. Ahora deberías ver TODO el navbar');
        $this->command->line('   3. Si quieres asignar otros roles:');
        $this->command->line('      UPDATE usuario SET id_rol = X WHERE id = Y;');
        $this->command->line('──────────────────────────────────────────────────────');
        $this->command->newLine();

        $this->command->warn('💡 TIP: Para asignar roles a otros usuarios:');
        $this->command->newLine();
        $this->command->line('SELECT id, nombre FROM rol;  -- Ver IDs de roles');
        $this->command->line('UPDATE usuario SET id_rol = 2 WHERE id = 3;  -- Asignar rol');
        $this->command->newLine();
    }
}
