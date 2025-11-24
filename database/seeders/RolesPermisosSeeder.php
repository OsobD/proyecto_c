<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesPermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Este seeder ejecuta los seeders de permisos y roles en orden
     *
     * Uso: php artisan db:seed --class=RolesPermisosSeeder
     */
    public function run(): void
    {
        $this->command->info('🔐 Iniciando configuración del sistema de permisos granulares...');
        $this->command->newLine();

        // 1. Crear permisos
        $this->command->info('📝 Paso 1/2: Creando permisos del sistema...');
        $this->call(PermisosSeeder::class);
        $this->command->newLine();

        // 2. Crear roles con sus permisos
        $this->command->info('👥 Paso 2/2: Creando roles predefinidos...');
        $this->call(RolesSeeder::class);
        $this->command->newLine();

        $this->command->info('✅ ¡Sistema de permisos configurado exitosamente!');
        $this->command->newLine();

        $this->command->line('──────────────────────────────────────────────────────');
        $this->command->info('📚 Roles creados:');
        $this->command->line('  1. Colaborador de Bodega (operativo)');
        $this->command->line('  2. Jefe de Bodega (supervisor y aprobador)');
        $this->command->line('  3. Colaborador de Contabilidad (solo reportes)');
        $this->command->line('  4. Administrador TI (control total)');
        $this->command->line('──────────────────────────────────────────────────────');
        $this->command->newLine();

        $this->command->warn('⚠️  PRÓXIMOS PASOS:');
        $this->command->line('   1. Asigna un rol a cada usuario en la tabla `usuario`');
        $this->command->line('   2. Consulta ROLES_Y_PERMISOS.md para la guía completa');
        $this->command->line('   3. El navbar ya es dinámico según permisos');
        $this->command->line('   4. Usa @can(\'permiso.nombre\') en las vistas Blade');
        $this->command->newLine();

        $this->command->info('📖 Documentación:');
        $this->command->line('   - ROLES_Y_PERMISOS.md → Guía rápida para usuarios');
        $this->command->line('   - ARQUITECTURA_PERMISOS.md → Arquitectura técnica');
        $this->command->newLine();
    }
}

