<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Persona;
use App\Models\Rol;
use App\Models\TarjetaResponsabilidad;
use Illuminate\Support\Facades\Hash;

class UsuarioAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar el rol de Administrador
        $rolAdmin = Rol::where('nombre', 'Administrador TI')->first();

        // Crear o actualizar persona para el administrador
        $persona = Persona::updateOrCreate(
            ['dpi' => '0000000000000'],
            [
                'nombres' => 'Administrador',
                'apellidos' => 'del Sistema',
                'telefono' => '0000-0000',
                'correo' => 'admin@eemq.com',
                'estado' => true,
            ]
        );

        // Crear o actualizar tarjeta de responsabilidad
        TarjetaResponsabilidad::firstOrCreate(
            ['id_persona' => $persona->id],
            [
                'nombre' => 'Administrador del Sistema',
                'fecha_creacion' => now(),
                'total' => 0,
                'activo' => true,
            ]
        );

        // Crear o actualizar usuario administrador
        Usuario::updateOrCreate(
            ['nombre_usuario' => 'admin'],
            [
                'contrasena' => Hash::make('admin123'),
                'id_persona' => $persona->id,
                'id_rol' => $rolAdmin?->id,
                'estado' => true,
                'debe_cambiar_contrasena' => false, // Admin no necesita cambiar contraseña
            ]
        );
    }
}
