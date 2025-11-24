# 🔐 Guía Rápida: Roles y Permisos - Sistema EEMQ

> **Guía de referencia rápida para gestionar roles y permisos en el sistema de inventario**

---

## 📋 Tabla de Roles y Accesos

| Módulo | Colaborador Bodega | Jefe Bodega | Colaborador Contabilidad | Admin TI |
|--------|-------------------|-------------|-------------------------|----------|
| **Compras** | ✅ Ver, Crear<br>⏳ Editar (con aprobación) | ✅ Ver, Crear<br>✅ Editar directo<br>✅ Eliminar | ❌ | ✅ Total |
| **Traslados** | ✅ Ver, Crear<br>⏳ Editar (con aprobación) | ✅ Ver, Crear<br>✅ Editar directo<br>✅ Eliminar | ❌ | ✅ Total |
| **Requisiciones** | ✅ Ver, Crear<br>⏳ Editar (con aprobación) | ✅ Ver, Crear<br>✅ Editar directo<br>✅ Eliminar | ❌ | ✅ Total |
| **Devoluciones** | ✅ Ver, Crear<br>⏳ Editar (con aprobación) | ✅ Ver, Crear<br>✅ Editar directo<br>✅ Eliminar | ❌ | ✅ Total |
| **Productos** | ✅ Ver, Crear<br>⏳ Editar (con aprobación) | ✅ Ver, Crear<br>✅ Editar directo<br>✅ Eliminar | ❌ | ✅ Total |
| **Categorías** | ✅ Ver, Crear<br>⏳ Editar (con aprobación) | ✅ Ver, Crear<br>✅ Editar directo<br>✅ Eliminar | ❌ | ✅ Total |
| **Proveedores** | ✅ Ver, Crear<br>⏳ Editar (con aprobación) | ✅ Ver, Crear<br>✅ Editar directo<br>✅ Eliminar | ❌ | ✅ Total |
| **Personas** | ✅ Ver, Crear<br>⏳ Editar (con aprobación) | ✅ Ver, Crear<br>✅ Editar directo<br>✅ Eliminar | ❌ | ✅ Total |
| **Bodegas** | 👁️ Solo ver | ✅ Ver, Crear<br>✅ Editar directo<br>✅ Eliminar | ❌ | ✅ Total |
| **Tarjetas** | 👁️ Solo ver | ✅ Ver, Crear<br>✅ Editar directo<br>✅ Eliminar | ❌ | ✅ Total |
| **Usuarios** | ❌ | ❌ | ❌ | ✅ Total |
| **Puestos** | ❌ | ❌ | ❌ | ✅ Total |
| **Reportes** | ❌ | ✅ Todos | ✅ Todos (solo lectura) | ✅ Total |
| **Bitácora** | ❌ | ✅ Ver | ✅ Ver | ✅ Total |
| **Configuración** | ❌ | ❌ | ❌ | ✅ Total |
| **Aprobaciones** | ❌ Ver propias | ✅ Aprobar/Rechazar | ❌ | ✅ Aprobar/Rechazar |

**Leyenda:**
- ✅ = Acceso completo
- 👁️ = Solo lectura
- ⏳ = Requiere aprobación
- ❌ = Sin acceso

---

## 🎭 Descripción de Roles

### 1️⃣ Colaborador de Bodega
**Perfil**: Personal operativo de bodega que registra movimientos diarios

**Puede hacer**:
- Registrar compras, traslados, requisiciones y devoluciones
- Agregar productos, categorías, proveedores y personas
- Ver bodegas y tarjetas de responsabilidad
- Solicitar ediciones (van a aprobación)

**NO puede hacer**:
- Editar directamente (requiere aprobación)
- Eliminar o desactivar registros
- Gestionar usuarios o puestos
- Ver reportes o bitácora
- Aprobar cambios

---

### 2️⃣ Jefe de Bodega
**Perfil**: Supervisor de operaciones de bodega

**Puede hacer TODO lo que el Colaborador de Bodega, ADEMÁS**:
- Editar directamente sin aprobación
- Eliminar/desactivar registros
- Gestionar bodegas y tarjetas de responsabilidad
- Ver todos los reportes y bitácora
- **Aprobar o rechazar cambios** solicitados por colaboradores

**NO puede hacer**:
- Gestionar usuarios o puestos
- Modificar configuración del sistema

---

### 3️⃣ Colaborador de Contabilidad
**Perfil**: Personal de contabilidad que necesita consultar información para auditoría

**Puede hacer**:
- Ver TODOS los reportes (solo lectura)
- Ver bitácora del sistema (solo lectura)

**NO puede hacer**:
- Crear, editar o eliminar NADA
- Aprobar cambios
- Acceder a otras secciones operativas

---

### 4️⃣ Administrador TI
**Perfil**: Control total del sistema

**Puede hacer**:
- TODO sin restricciones
- Gestionar usuarios y puestos
- Configurar el sistema
- Gestionar roles y permisos personalizados
- Aprobar cualquier cambio

---

## 🔧 Crear Roles Personalizados

El sistema permite crear roles personalizados (ej: "Limpiador Bodega", "Supervisor Nocturno", etc.)

### Pasos para crear un rol personalizado:

1. Ve a **Configuración** → **Roles**
2. Click en **+ Nuevo Rol**
3. Asigna un nombre descriptivo
4. Selecciona los permisos específicos que necesita
5. Guarda

### Ejemplo: Rol "Limpiador Bodega"
**Permisos sugeridos**:
- ✅ `bodegas.acceder` - Ver bodegas
- ✅ `productos.acceder` - Ver productos
- ❌ No más permisos

---

## 📌 Sistema de Aprobaciones

### ¿Cómo funciona?

Cuando un **Colaborador de Bodega** intenta editar un registro:

1. **Colaborador** hace clic en "Editar" en una compra
2. Modifica los campos necesarios
3. Al guardar, aparece: *"Cambios enviados para aprobación"*
4. El registro original **NO se modifica** aún
5. Se crea una **Tarea Pendiente** para el Jefe/Admin

6. **Jefe de Bodega** ve en **Reportes** → **Aprobaciones Pendientes**:
   - Lista de cambios solicitados
   - Comparación antes/después
   - Quién solicitó el cambio y cuándo

7. **Jefe** puede:
   - ✅ **Aprobar** → Los cambios se aplican al registro original
   - ❌ **Rechazar** → El registro permanece sin cambios
   - Agregar comentarios/justificación

8. **Colaborador** recibe notificación del resultado

### Ventaja del sistema de tareas:
- El colaborador no queda bloqueado esperando aprobación
- Puede seguir trabajando en otras cosas
- El jefe revisa cuando tenga tiempo
- Historial completo de cambios propuestos

---

## 🔑 Estructura de Permisos

Formato: `modulo.accion[.modificador]`

### Ejemplos:

| Permiso | Descripción |
|---------|-------------|
| `compras.acceder` | Puede ver la página de compras |
| `compras.crear` | Puede crear compras |
| `compras.editar` | Puede editar compras (con aprobación) |
| `compras.editar.sin_aprobacion` | Puede editar compras directamente |
| `compras.eliminar` | Puede eliminar/desactivar compras |
| `compras.aprobar` | Puede aprobar cambios en compras |

### Módulos disponibles:
- `compras`
- `traslados`
- `requisiciones`
- `devoluciones`
- `productos`
- `categorias`
- `proveedores`
- `personas`
- `bodegas`
- `tarjetas`
- `usuarios`
- `puestos`
- `reportes`
- `bitacora`
- `configuracion`

---

## 🚀 Cómo probar el sistema

### 1. Crear usuarios de prueba

Ejecuta el seeder:
```bash
php artisan db:seed --class=RolesPermisosSeeder
```

### 2. Usuarios de prueba creados automáticamente:

| Usuario | Contraseña | Rol |
|---------|-----------|-----|
| `colaborador@eemq.com` | `password` | Colaborador de Bodega |
| `jefe@eemq.com` | `password` | Jefe de Bodega |
| `contabilidad@eemq.com` | `password` | Colaborador de Contabilidad |
| `admin@eemq.com` | `password` | Administrador TI |

### 3. Flujo de prueba:

**Como Colaborador de Bodega**:
1. Login con `colaborador@eemq.com`
2. Ve a Compras → Nueva Compra
3. Crea una compra (se registra directamente)
4. Intenta editar la compra → envía a aprobación
5. Intenta eliminar → botón no visible

**Como Jefe de Bodega**:
1. Login con `jefe@eemq.com`
2. Ve a Reportes → Aprobaciones Pendientes
3. Revisa la solicitud del colaborador
4. Aprueba o rechaza

**Como Colaborador de Contabilidad**:
1. Login con `contabilidad@eemq.com`
2. Solo ve: Reportes (todo el navbar oculto)
3. Puede consultar pero no modificar nada

**Como Admin TI**:
1. Login con `admin@eemq.com`
2. Acceso completo a todo
3. Puede gestionar roles y permisos

---

## 💡 Tips y Buenas Prácticas

### Para Administradores:
1. **No elimines roles predefinidos** - otros usuarios pueden depender de ellos
2. **Documenta roles personalizados** - agrega descripción clara
3. **Revisa permisos periódicamente** - audita qué puede hacer cada rol
4. **Usa el principio de menor privilegio** - solo da los permisos necesarios

### Para Jefes de Bodega:
1. **Revisa aprobaciones diariamente** - no dejes colaboradores esperando mucho
2. **Agrega comentarios al aprobar/rechazar** - ayuda al aprendizaje
3. **Revisa la bitácora semanalmente** - detecta patrones inusuales

### Para Colaboradores:
1. **Agrega justificación al editar** - facilita la aprobación
2. **Verifica antes de crear** - evita duplicados
3. **Consulta con el jefe si tienes dudas** - antes de solicitar cambios

---

## 🔍 Navbar Dinámico

El navbar se ajusta automáticamente según los permisos del usuario:

### Colaborador de Bodega verá:
```
[Logo] Compras | Traslados | Catálogo | Colaboradores | Almacenes | [Usuario]
```

### Jefe de Bodega verá:
```
[Logo] Compras | Traslados | Catálogo | Colaboradores | Almacenes | Reportes | [Usuario]
```

### Colaborador de Contabilidad verá:
```
[Logo] Reportes | [Usuario]
```

### Admin TI verá:
```
[Logo] Compras | Traslados | Catálogo | Colaboradores | Almacenes | Reportes | Configuración | [Usuario]
```

**Nota**: Si un dropdown solo tiene 1 elemento visible, se muestra como link directo en lugar de dropdown.

---

## 📞 Soporte

Si tienes problemas con permisos:
1. Verifica que el usuario tenga asignado un rol
2. Verifica que el rol tenga los permisos necesarios
3. Limpia la caché: `php artisan cache:clear`
4. Contacta al Administrador TI

---

**Última actualización**: 2025-01-23
**Versión**: 1.0
**Sistema**: EEMQ - Inventario
