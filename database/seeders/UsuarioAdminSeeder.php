<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Persona;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;

class UsuarioAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar el rol de Administrador
        $rolAdmin = Rol::where('nombre', 'Administrador')->first();

        // Crear persona para el administrador
        $persona = Persona::create([
            'nombres' => 'Administrador',
            'apellidos' => 'del Sistema',
            'telefono' => '0000-0000',
            'correo' => 'admin@eemq.com',
            'fecha_nacimiento' => now()->subYears(30),
            'genero' => 'M',
            'estado' => true,
        ]);

        // Crear usuario administrador
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@eemq.com',
            'password' => Hash::make('admin123'),  // Contraseña: admin123
            'id_persona' => $persona->id,
            'id_rol' => $rolAdmin?->id,
            'estado' => true,
            'email_verified_at' => now(),
        ]);
    }
}
