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

## 2. Roles Definidos (Roles Reales del Sistema)

### ROL: Colaborador de Bodega
**Descripción**: Usuario operativo que registra movimientos diarios de inventario

**Accesos**:
- ✅ Compras: acceder, crear
- ✅ Traslados: acceder, crear
- ✅ Requisiciones: acceder, crear
- ✅ Devoluciones: acceder, crear
- ✅ Productos: acceder, crear
- ✅ Categorías: acceder, crear
- ✅ Proveedores: acceder, crear
- ✅ Personas: acceder, crear
- ✅ Bodegas: acceder (solo ver)
- ✅ Tarjetas: acceder (solo ver)
- ❌ Usuarios: NO accede
- ❌ Puestos: NO accede
- ❌ Bitácora: NO accede
- ❌ Reportes: NO accede
- ❌ Configuración: NO accede

**Restricciones**:
- Puede CREAR todo lo que tiene acceso (se registra directamente)
- Puede EDITAR pero requiere aprobación (genera tarea para Jefe/Admin)
- NO puede ELIMINAR/DESACTIVAR

---

### ROL: Jefe de Bodega
**Descripción**: Supervisa operaciones de bodega y aprueba cambios

**Hereda de**: Colaborador de Bodega

**Permisos adicionales**:
- ✅ Aprobar cambios en: compras, traslados, requisiciones, devoluciones
- ✅ Editar SIN aprobación: productos, categorías, proveedores, personas
- ✅ Gestionar: bodegas, tarjetas (crear, editar, desactivar)
- ✅ Reportes: acceder a todos los reportes
- ✅ Bitácora: acceder (vía Reportes)
- ✅ Ver tareas pendientes de aprobación
- ❌ Usuarios: NO accede
- ❌ Puestos: NO accede
- ❌ Configuración: NO accede

---

### ROL: Colaborador de Contabilidad
**Descripción**: Consulta reportes y bitácora para auditoría

**Accesos**:
- ✅ Reportes: acceder a todos los reportes (solo lectura)
- ✅ Bitácora: acceder (vía Reportes, solo lectura)
- ❌ NO puede crear, editar o eliminar NADA
- ❌ NO accede a ninguna otra sección del sistema

**Restricciones**:
- Solo lectura en Reportes y Bitácora
- No puede aprobar cambios
- No puede exportar datos sensibles (opcional, según necesidad)

---

### ROL: Administrador TI
**Descripción**: Control total del sistema, configuración y gestión de usuarios

**Permisos**:
- ✅ TODO sin restricciones
- ✅ Gestión de usuarios y puestos
- ✅ Configuración del sistema
- ✅ Gestión de roles y permisos
- ✅ Bitácora completa
- ✅ Aprobar cualquier cosa
- ✅ Acceso a todas las funcionalidades

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

### Caso 1: Colaborador crea una compra (NO requiere aprobación)
```
1. Colaborador de Bodega llena formulario de compra
2. Al guardar, se crea registro DIRECTAMENTE en `compras`
3. Se registra en bitácora automáticamente
4. La compra está disponible inmediatamente en el sistema
```

### Caso 2: Jefe de Bodega edita un producto (SIN aprobación)
```
1. Jefe de Bodega edita producto
2. Tiene permiso `productos.editar.sin_aprobacion`
3. Cambio se aplica DIRECTAMENTE sin pasar por aprobaciones
4. Se registra en bitácora
```

### Caso 3: Colaborador edita una compra (CON aprobación - Sistema de Tareas)
```
1. Colaborador de Bodega ve botón "Editar" (habilitado)
2. Llena formulario con cambios propuestos
3. Al guardar:
   a. El registro original NO se modifica aún
   b. Se crea registro en `cambios_pendientes` con:
      - datos_anteriores (estado actual)
      - datos_nuevos (cambios propuestos)
      - estado = 'pendiente'
      - tipo = 'edicion'
   c. Se muestra mensaje: "Cambios enviados para aprobación"
4. Jefe de Bodega / Admin TI ve en "Tareas Pendientes" (en Reportes):
   - Lista de cambios pendientes
   - Comparación lado a lado (antes/después)
   - Justificación del colaborador (si se agregó)
5. Jefe aprueba o rechaza:
   - Si APRUEBA → se actualiza el registro original con datos_nuevos
   - Si RECHAZA → se marca como rechazado, registro original no cambia
6. Colaborador ve notificación del resultado
```

### Caso 4: Colaborador intenta eliminar (SIN permiso)
```
1. Colaborador de Bodega NO ve botón de eliminar/desactivar
2. El botón está oculto por @can('compras.eliminar')
3. Si intenta acceder directo por URL → middleware bloquea con 403
```

### Caso 5: Administrador TI gestiona usuarios (SIN restricciones)
```
1. Admin TI accede a /usuarios
2. Puede crear, editar, eliminar directamente
3. No requiere aprobaciones
4. Todo se registra en bitácora
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
