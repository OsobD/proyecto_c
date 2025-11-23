# Arquitectura de Permisos - Sistema de Inventario EEMQ

## 1. Estructura de Permisos

### Formato: `modulo.accion[.modificador]`

```
Ejemplos:
- usuarios.acceder
- usuarios.crear
- usuarios.editar
- usuarios.editar.sin_aprobacion
- usuarios.eliminar
- usuarios.aprobar

- compras.acceder
- compras.crear
- compras.editar
- compras.editar.sin_aprobacion
- compras.eliminar
- compras.aprobar
```

## 2. Roles Definidos

### ROL: Colaborador (Operativo)
**Descripción**: Usuario operativo que registra movimientos diarios

**Accesos**:
- ✅ Compras: acceder, crear
- ✅ Traslados: acceder, crear
- ✅ Requisiciones: acceder, crear
- ✅ Devoluciones: acceder, crear
- ✅ Productos: acceder, crear
- ✅ Categorías: acceder, crear
- ✅ Proveedores: acceder, crear
- ✅ Bodegas: acceder (solo ver)
- ✅ Tarjetas: acceder (solo ver)
- ❌ Usuarios: NO accede
- ❌ Puestos: NO accede
- ❌ Bitácora/Reportes: NO accede
- ❌ Configuración: NO accede

**Restricciones**:
- Puede CREAR todo lo que tiene acceso
- NO puede EDITAR sin aprobación
- NO puede ELIMINAR/DESACTIVAR

---

### ROL: Supervisor
**Descripción**: Puede crear y APROBAR cambios

**Hereda de**: Colaborador

**Permisos adicionales**:
- ✅ Aprobar cambios en: compras, traslados, requisiciones, devoluciones
- ✅ Editar SIN aprobación: productos, categorías, proveedores
- ✅ Gestionar: bodegas, tarjetas (crear, editar)
- ✅ Reportes: acceder

---

### ROL: Administrador
**Descripción**: Control total del sistema

**Permisos**:
- ✅ TODO sin restricciones
- ✅ Gestión de usuarios
- ✅ Configuración del sistema
- ✅ Bitácora completa
- ✅ Aprobar cualquier cosa

---

## 3. Tabla de Permisos (Database)

### Estructura sugerida:

| ID | Módulo | Acción | Modificador | Nombre Display | Descripción |
|----|--------|--------|-------------|----------------|-------------|
| 1 | usuarios | acceder | null | Acceder a Usuarios | Puede ver la página de usuarios |
| 2 | usuarios | crear | null | Crear Usuarios | Puede crear usuarios (requiere aprobación) |
| 3 | usuarios | crear | sin_aprobacion | Crear Usuarios (directo) | Puede crear usuarios sin aprobación |
| 4 | usuarios | editar | null | Editar Usuarios | Puede editar usuarios (requiere aprobación) |
| 5 | usuarios | editar | sin_aprobacion | Editar Usuarios (directo) | Puede editar usuarios sin aprobación |
| 6 | usuarios | eliminar | null | Eliminar Usuarios | Puede eliminar/desactivar usuarios |
| 7 | usuarios | aprobar | null | Aprobar Cambios en Usuarios | Puede aprobar cambios pendientes |
| 8 | compras | acceder | null | Acceder a Compras | Puede ver compras |
| ... | ... | ... | ... | ... | ... |

---

## 4. Navegación Dinámica

### Archivo: app/Services/NavigationService.php

```php
class NavigationService
{
    public static function getMenuItems()
    {
        $user = auth()->user();

        $menu = [
            [
                'label' => 'Inicio',
                'route' => 'dashboard',
                'icon' => 'home',
                'permission' => null, // Todos acceden
            ],
            [
                'label' => 'Compras',
                'route' => 'compras',
                'icon' => 'shopping-cart',
                'permission' => 'compras.acceder',
            ],
            [
                'label' => 'Traslados',
                'route' => 'traslados',
                'icon' => 'truck',
                'permission' => 'traslados.acceder',
            ],
            // ... más items
            [
                'label' => 'Usuarios',
                'route' => 'usuarios',
                'icon' => 'users',
                'permission' => 'usuarios.acceder',
            ],
            [
                'label' => 'Reportes',
                'route' => 'reportes',
                'icon' => 'chart',
                'permission' => 'reportes.acceder',
                'children' => [
                    [
                        'label' => 'Bitácora del Sistema',
                        'route' => 'reportes.bitacora',
                        'permission' => 'bitacora.acceder',
                    ],
                ],
            ],
        ];

        // Filtrar por permisos
        return collect($menu)->filter(function ($item) use ($user) {
            return is_null($item['permission']) || $user->tienePermiso($item['permission']);
        })->values()->all();
    }
}
```

---

## 5. Sistema de Aprobaciones

### Tabla: `cambios_pendientes`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT | Primary key |
| modelo | VARCHAR | Modelo afectado (Compra, Usuario, etc.) |
| modelo_id | INT | ID del registro (null si es creación) |
| accion | ENUM | 'crear', 'editar', 'eliminar' |
| datos_nuevos | JSON | Datos nuevos/propuestos |
| datos_anteriores | JSON | Datos anteriores (solo en edición) |
| usuario_solicitante_id | INT | Quién solicitó el cambio |
| estado | ENUM | 'pendiente', 'aprobado', 'rechazado' |
| usuario_aprobador_id | INT | Quién aprobó/rechazó |
| fecha_aprobacion | TIMESTAMP | Cuándo se aprobó/rechazó |
| observaciones | TEXT | Notas del aprobador |
| created_at | TIMESTAMP | Cuándo se solicitó |

---

## 6. Flujo de Trabajo

### Caso 1: Colaborador crea una compra
```
1. Colaborador llena formulario de compra
2. Al guardar, se crea registro en `cambios_pendientes` con estado='pendiente'
3. Se notifica a supervisores
4. Compra NO aparece en el sistema hasta aprobación
5. Supervisor aprueba → se crea el registro real en `compras`
6. Se marca el cambio_pendiente como 'aprobado'
```

### Caso 2: Supervisor edita un producto
```
1. Supervisor edita producto
2. Tiene permiso `productos.editar.sin_aprobacion`
3. Cambio se aplica DIRECTAMENTE sin pasar por aprobaciones
4. Se registra en bitácora
```

### Caso 3: Colaborador intenta editar una compra
```
1. Colaborador ve botón "Solicitar Edición" (no "Editar")
2. Llena formulario con cambios propuestos
3. Se crea registro en `cambios_pendientes` con datos_anteriores + datos_nuevos
4. Supervisor compara cambios y aprueba/rechaza
5. Si aprueba → se actualiza el registro original
```

---

## 7. Bitácora como Reporte

**SÍ, definitivamente mover bitácora a reportes**

### Razones:
1. La bitácora NO es una función operativa, es AUDITORÍA
2. No todos deben tener acceso (colaboradores no)
3. Es un reporte más: "Reporte de Actividad del Sistema"
4. Simplifica navegación

### Estructura sugerida en Reportes:

```
/reportes
├── Compras
├── Traslados
├── Inventario
├── Bitácora ⭐ (nuevo)
│   ├── Filtros:
│   │   ├── Usuario
│   │   ├── Módulo (Usuarios, Compras, Productos, etc.)
│   │   ├── Acción (Crear, Editar, Eliminar, Aprobar, etc.)
│   │   ├── Rango de fechas
│   └── Exportar a Excel/PDF
└── Aprobaciones Pendientes ⭐ (nuevo)
    ├── Lista de cambios pendientes
    ├── Botones: Aprobar / Rechazar / Ver Detalles
```

---

## 8. Navbar Dinámica

### Componente: `components/layouts/app.blade.php`

```blade
<nav>
    @foreach(App\Services\NavigationService::getMenuItems() as $item)
        <a href="{{ route($item['route']) }}"
           class="nav-link">
            <i class="icon-{{ $item['icon'] }}"></i>
            {{ $item['label'] }}
        </a>

        @if(isset($item['children']) && count($item['children']) > 0)
            <div class="dropdown">
                @foreach($item['children'] as $child)
                    <a href="{{ route($child['route']) }}">
                        {{ $child['label'] }}
                    </a>
                @endforeach
            </div>
        @endif
    @endforeach
</nav>
```

---

## 9. Middleware de Permisos

### Archivo: `app/Http/Middleware/CheckPermission.php`

```php
class CheckPermission
{
    public function handle($request, Closure $next, $permission)
    {
        if (!auth()->user()->tienePermiso($permission)) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }

        return $next($request);
    }
}
```

### Uso en rutas:

```php
Route::middleware(['auth', 'permission:usuarios.acceder'])
    ->get('/usuarios', GestionUsuarios::class)
    ->name('usuarios');

Route::middleware(['auth', 'permission:bitacora.acceder'])
    ->get('/reportes/bitacora', BitacoraSistema::class)
    ->name('reportes.bitacora');
```

---

## 10. Helpers para vistas

### Model Usuario:

```php
public function puede($permiso)
{
    return $this->tienePermiso($permiso);
}

public function puedeCrear($modulo)
{
    return $this->tienePermiso("{$modulo}.crear") ||
           $this->tienePermiso("{$modulo}.crear.sin_aprobacion");
}

public function puedeEditarDirecto($modulo)
{
    return $this->tienePermiso("{$modulo}.editar.sin_aprobacion");
}

public function puedeAprobar($modulo)
{
    return $this->tienePermiso("{$modulo}.aprobar");
}
```

### Uso en vistas:

```blade
@can('usuarios.crear')
    <button wire:click="abrirModal">+ Nuevo Usuario</button>
@endcan

@can('usuarios.editar.sin_aprobacion')
    <button wire:click="editar({{ $usuario->id }})">Editar</button>
@else
    <button wire:click="solicitarEdicion({{ $usuario->id }})">Solicitar Edición</button>
@endcan

@can('usuarios.aprobar')
    <span class="badge">{{ $cambiosPendientes }} pendientes</span>
@endcan
```
