# 🚀 Instalación del Sistema de Permisos Granulares

> Sistema completo de permisos granulares con aprobaciones para EEMQ - Inventario

---

## 📋 Requisitos Previos

- Base de datos configurada
- Laravel 11+ instalado
- Composer instalado

---

## ⚙️ Pasos de Instalación

### 1. Ejecutar Migraciones

Las migraciones modifican las tablas existentes de `permiso` y `rol`, y crean la tabla `cambios_pendientes`:

```bash
php artisan migrate
```

**Migraciones incluidas**:
- `add_granular_fields_to_permiso_table` - Agrega campos módulo, acción, modificador
- `create_cambios_pendientes_table` - Sistema de aprobaciones
- `add_descripcion_to_rol_table` - Mejora tabla rol

### 2. Ejecutar Seeders

Esto creará 95+ permisos y 4 roles predefinidos:

```bash
php artisan db:seed --class=RolesPermisosSeeder
```

**¿Qué hace?**:
1. Crea todos los permisos del sistema (compras.crear, productos.editar, etc.)
2. Crea 4 roles:
   - Colaborador de Bodega
   - Jefe de Bodega
   - Colaborador de Contabilidad
   - Administrador TI
3. Asigna permisos a cada rol

### 3. Asignar Roles a Usuarios

**IMPORTANTE**: Debes asignar un rol a cada usuario existente.

**Opción A: Manualmente en la base de datos**

```sql
-- Ver roles disponibles
SELECT id, nombre FROM rol;

-- Asignar rol a usuario
UPDATE usuario SET id_rol = 4 WHERE id = 1; -- Administrador TI
UPDATE usuario SET id_rol = 2 WHERE id = 2; -- Jefe de Bodega
UPDATE usuario SET id_rol = 1 WHERE id = 3; -- Colaborador de Bodega
```

**Opción B: Crear un seeder de usuarios**

```php
<?php
use App\Models\Usuario;
use App\Models\Rol;

// Obtener roles
$adminTI = Rol::where('nombre', 'Administrador TI')->first();
$jefeBodega = Rol::where('nombre', 'Jefe de Bodega')->first();

// Asignar rol a usuario específico
$usuario = Usuario::find(1);
$usuario->id_rol = $adminTI->id;
$usuario->save();
```

---

## ✅ Verificación

### 1. Verificar Permisos

```bash
php artisan tinker
```

```php
// Ver todos los permisos
\App\Models\Permiso::count(); // Debe ser 95+

// Ver permisos de un módulo
\App\Models\Permiso::where('modulo', 'compras')->get();

// Ver roles
\App\Models\Rol::with('permisos')->get();
```

### 2. Verificar Navbar Dinámico

1. Inicia sesión con diferentes usuarios
2. El navbar debe mostrar solo las opciones permitidas según el rol
3. **Colaborador de Contabilidad** solo verá "Reportes"
4. **Administrador TI** verá TODO

### 3. Probar Permisos en Código

```php
// En un controlador o componente Livewire
$user = auth()->user();

// Verificar permiso específico
if ($user->tienePermiso('compras.crear')) {
    // Puede crear compras
}

// Verificar si puede editar directamente
if ($user->puedeEditarDirecto('productos')) {
    // Puede editar sin aprobación
}

// Verificar si es administrador
if ($user->esAdministrador()) {
    // Es admin
}
```

### 4. Usar en Vistas Blade

```blade
{{-- Mostrar botón solo si tiene permiso --}}
@can('compras.crear')
    <button>+ Nueva Compra</button>
@endcan

{{-- Mostrar editar o solicitar edición --}}
@can('compras.editar.sin_aprobacion')
    <button wire:click="editar({{ $compra->id }})">Editar</button>
@else
    @can('compras.editar')
        <button wire:click="solicitarEdicion({{ $compra->id }})">Solicitar Edición</button>
    @endcan
@endcan

{{-- Verificar si es jefe o admin --}}
@can('puede-aprobar')
    <a href="{{ route('reportes') }}?tab=aprobaciones">
        Aprobaciones Pendientes ({{ $pendientes }})
    </a>
@endcan
```

---

## 🔑 Roles y Permisos

### Colaborador de Bodega (28 permisos)
- ✅ Crear: compras, traslados, requisiciones, devoluciones, productos, categorías, proveedores, personas
- ⏳ Editar: requiere aprobación
- ❌ Eliminar: no puede
- ❌ Usuarios, Puestos, Reportes, Bitácora: sin acceso

### Jefe de Bodega (54 permisos)
- ✅ TODO lo que puede Colaborador
- ✅ Editar SIN aprobación
- ✅ Eliminar/desactivar
- ✅ Gestionar bodegas y tarjetas
- ✅ Ver reportes y bitácora
- ✅ **Aprobar cambios**

### Colaborador de Contabilidad (2 permisos)
- ✅ Solo reportes y bitácora (lectura)
- ❌ No puede crear, editar ni eliminar NADA

### Administrador TI (TODOS los permisos)
- ✅ Control total
- ✅ Gestionar usuarios y puestos
- ✅ Configurar roles y permisos
- ✅ TODO sin restricciones

---

## 📖 Documentación Adicional

- **ROLES_Y_PERMISOS.md** - Guía rápida para usuarios
- **ARQUITECTURA_PERMISOS.md** - Arquitectura técnica detallada

---

## 🐛 Troubleshooting

### Error: "No tienes permiso para acceder a esta página"

**Causa**: El usuario no tiene rol asignado o el rol no tiene el permiso requerido.

**Solución**:
```sql
-- Verificar rol del usuario
SELECT u.id, u.nombre_usuario, r.nombre as rol
FROM usuario u
LEFT JOIN rol r ON u.id_rol = r.id;

-- Asignar rol si es NULL
UPDATE usuario SET id_rol = 4 WHERE id = 1;
```

### El navbar no se actualiza

**Solución**: Limpia la caché

```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

### Los permisos no funcionan

**Verificar** que el AuthServiceProvider esté registrado:

```bash
# El archivo debe existir
ls app/Providers/AuthServiceProvider.php

# Debe auto-cargarse en Laravel 11
# Si no funciona, agregarlo manualmente en bootstrap/providers.php
```

---

## 🎯 Próximos Pasos

1. ✅ Asignar roles a todos los usuarios
2. ✅ Probar el sistema con diferentes roles
3. ✅ Implementar componente de Aprobaciones Pendientes (opcional)
4. ✅ Personalizar permisos según necesidades
5. ✅ Crear roles personalizados si es necesario

---

## 💡 Crear Roles Personalizados

**Via Interfaz** (recomendado):
1. Ve a Configuración → Roles
2. Click en "+ Nuevo Rol"
3. Selecciona los permisos necesarios
4. Guarda

**Via Código**:
```php
use App\Models\Rol;
use App\Models\Permiso;

// Crear rol
$rol = Rol::create([
    'nombre' => 'Limpiador Bodega',
    'descripcion' => 'Solo puede ver bodegas y productos',
    'es_sistema' => false,
]);

// Asignar permisos
$permisos = Permiso::whereIn('nombre', [
    'bodegas.acceder',
    'productos.acceder',
])->pluck('id');

$rol->permisos()->sync($permisos);
```

---

**¡Sistema de Permisos Listo!** 🎉

Para soporte, consulta la documentación o contacta al equipo de desarrollo.
