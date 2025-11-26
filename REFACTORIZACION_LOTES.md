# Refactorización del Sistema de Lotes

## Resumen de Cambios

Este documento describe la refactorización completa del sistema de lotes para hacerlos independientes de las bodegas.

### Problema Original

En el sistema anterior, cada vez que un producto se movía entre bodegas (compra → traslado → requisición), se creaba un **nuevo lote**. Esto resultaba en:

- Un mismo producto con múltiples lotes solo por cambiar de ubicación
- Pérdida de trazabilidad del lote original de compra
- Inconsistencia conceptual: un "lote" debería representar una compra específica, no una ubicación

### Solución Implementada

**Concepto Clave:** Un lote = Una compra específica de productos (con su fecha y precio de ingreso)

Las ubicaciones del lote se rastrean en una tabla separada (`lote_bodega`).

---

## Cambios en Base de Datos

### 1. Nueva Tabla: `lote_bodega`

Rastrea la distribución de cada lote en diferentes bodegas.

```sql
CREATE TABLE lote_bodega (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_lote BIGINT UNSIGNED NOT NULL,
    id_bodega BIGINT UNSIGNED NOT NULL,
    cantidad INT DEFAULT 0 COMMENT 'Cantidad de este lote en esta bodega',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (id_lote) REFERENCES lote(id) ON DELETE CASCADE,
    FOREIGN KEY (id_bodega) REFERENCES bodega(id) ON DELETE CASCADE,
    UNIQUE KEY unique_lote_bodega (id_lote, id_bodega)
);
```

**Índices creados:**
- `idx_lote_bodega_lookup` (id_lote, id_bodega)
- `idx_bodega_lotes` (id_bodega)
- `idx_lote_cantidad` (id_lote, cantidad)
- `unique_lote_bodega` (id_lote, id_bodega)

### 2. Modificaciones en Tabla `lote`

- **Campo renombrado:** `cantidad` → `cantidad_disponible`
  - Ahora representa el stock total disponible del lote en TODAS las bodegas
- **Campo `id_bodega`:** Mantenido temporalmente para compatibilidad, pero ya no es la fuente de verdad

### 3. Migración Automática de Datos

Se creó una migración que automáticamente:
1. Toma todos los lotes existentes con `id_bodega`
2. Crea registros correspondientes en `lote_bodega`
3. Preserva toda la información existente

**Archivo:** `2025_11_25_000003_migrate_existing_lotes_to_lote_bodega.php`

---

## Cambios en Modelos

### Modelo `Lote` (Actualizado)

**Nuevas relaciones:**
```php
// Distribución del lote en bodegas
public function ubicaciones(): HasMany

// Solo ubicaciones con stock
public function ubicacionesConStock(): HasMany
```

**Nuevos métodos:**
```php
// Obtener cantidad en una bodega específica
public function cantidadEnBodega($idBodega): int

// Incrementar stock en una bodega
public function incrementarEnBodega($idBodega, $cantidad): bool

// Decrementar stock en una bodega
public function decrementarEnBodega($idBodega, $cantidad, $esConsumo = false): bool

// Mover stock entre bodegas (traslados)
public function moverEntreBodegas($idBodegaOrigen, $idBodegaDestino, $cantidad): bool

// Obtener lotes FIFO para un producto en una bodega
public static function obtenerLotesFIFO($idProducto, $idBodega, $soloActivos = true)

// Calcular stock total en una bodega
public static function stockTotalEnBodega($idProducto, $idBodega): int
```

**Accessors/Mutators para compatibilidad:**
```php
// Permite seguir usando $lote->cantidad en código legacy
public function getCantidadAttribute()
public function setCantidadAttribute($value)
```

### Nuevo Modelo: `LoteBodega`

**Relaciones:**
```php
public function lote(): BelongsTo
public function bodega(): BelongsTo
```

**Métodos útiles:**
```php
public static function obtenerOCrear($idLote, $idBodega): LoteBodega
public function incrementarCantidad($cantidad): bool
public function decrementarCantidad($cantidad): bool
public function tieneSuficienteStock($cantidadRequerida): bool
```

**Scopes:**
```php
scopeDelLote($idLote)
scopeEnBodega($idBodega)
scopeConStock() // Solo con cantidad > 0
```

---

## Cambios en Componentes Livewire

### 1. FormularioCompra.php

**Antes:**
```php
Lote::create([
    'cantidad' => $cantidad,
    'id_bodega' => $bodegaId,
    // ...
]);
```

**Después:**
```php
$lote = Lote::create([
    'cantidad_disponible' => $cantidad,
    'id_bodega' => $bodegaId, // Temporal
    // ...
]);

// Registrar ubicación del lote
$lote->incrementarEnBodega($bodegaId, $cantidad);
```

### 2. FormularioTraslado.php

**Antes:**
- Buscaba lote en bodega destino
- Si existía: incrementaba cantidad
- Si NO existía: creaba NUEVO lote

**Después:**
```php
// Mover el MISMO lote entre bodegas (no se crea uno nuevo)
$lote->moverEntreBodegas(
    $bodegaOrigen,
    $bodegaDestino,
    $cantidad
);
```

**Impacto:** Ya no se crean lotes nuevos en traslados, mantiene la identidad del lote original.

### 3. FormularioRequisicion.php

**Productos Consumibles:**
```php
// Reduce en bodega Y reduce cantidad_disponible (se consumió)
$loteModel->decrementarEnBodega($idBodega, $cantidad, true);
// El flag 'true' indica consumo real
```

**Productos No Consumibles:**
```php
// También reduce cantidad_disponible (está asignado, no disponible)
$loteModel->decrementarEnBodega($idBodega, $cantidad, true);
```

### 4. FormularioDevolucion.php

**Antes:**
- Buscaba lote en bodega destino
- Incrementaba cantidad o creaba nuevo lote

**Después:**
```php
// Devuelve al LOTE ORIGINAL
$lote->incrementarEnBodega($bodegaId, $cantidad);

// Reactiva si estaba inactivo
if (!$lote->estado) {
    $lote->estado = true;
    $lote->save();
}
```

### 5. GestionProductos.php

**Creación manual de lotes:**
```php
$lote = Lote::create([
    'cantidad_disponible' => $cantidad,
    // ...
]);

// Registrar ubicación
$lote->incrementarEnBodega($bodegaId, $cantidad);
```

**Edición de lotes:**
- Si cambia la cantidad: actualiza tanto el lote como `lote_bodega`
- Si cambia la bodega: mueve el lote completo a la nueva bodega

---

## Comportamiento por Tipo de Producto

### Productos Consumibles (`es_consumible = true`)

**En Traslado:**
- ✅ Reduce cantidad en `lote_bodega` origen
- ✅ Incrementa cantidad en `lote_bodega` destino
- ✅ `cantidad_disponible` del lote NO cambia (solo se movió)

**En Requisición:**
- ✅ Reduce cantidad en `lote_bodega` origen
- ✅ Reduce `cantidad_disponible` del lote (se consumió realmente)
- ✅ Registra en `consumible_persona`
- ✅ Si `cantidad_disponible` llega a 0 → `estado = false` (inactivo)

### Productos NO Consumibles (`es_consumible = false`)

**En Traslado:**
- ✅ Igual que consumibles
- ✅ Reduce en origen, aumenta en destino
- ✅ `cantidad_disponible` NO cambia

**En Requisición (asignación a persona):**
- ✅ Reduce cantidad en `lote_bodega` origen
- ✅ Reduce `cantidad_disponible` (está asignado, no disponible)
- ✅ Crea `tarjeta_producto`
- ✅ Si `cantidad_disponible` llega a 0 → `estado = false` (opcional)

**En Devolución:**
- ✅ Incrementa en `lote_bodega` destino
- ✅ Incrementa `cantidad_disponible`
- ✅ Si estaba inactivo → `estado = true` (se reactiva)

---

## Ejemplo Práctico: 3 Laptops Dell

### Compra inicial (15/01/2025):

```
Tabla: lote
┌────┬────────────────────┬──────────────────┬──────────────────┬───────────┐
│ id │ id_producto        │ cantidad_disp    │ cantidad_inicial │ estado    │
├────┼────────────────────┼──────────────────┼──────────────────┼───────────┤
│ 1  │ LAPTOP-DELL-LAT    │ 3                │ 3                │ true      │
└────┴────────────────────┴──────────────────┴──────────────────┴───────────┘

Tabla: lote_bodega
┌────┬─────────┬────────────┬──────────┐
│ id │ id_lote │ id_bodega  │ cantidad │
├────┼─────────┼────────────┼──────────┤
│ 1  │ 1       │ 5 (Central)│ 3        │
└────┴─────────┴────────────┴──────────┘
```

### Traslado a otra bodega (2 laptops):

```
Tabla: lote (sin cambios en cantidad_disponible)
┌────┬────────────────────┬──────────────────┬──────────────────┬───────────┐
│ id │ id_producto        │ cantidad_disp    │ cantidad_inicial │ estado    │
├────┼────────────────────┼──────────────────┼──────────────────┼───────────┤
│ 1  │ LAPTOP-DELL-LAT    │ 3                │ 3                │ true      │
└────┴────────────────────┴──────────────────┴──────────────────┴───────────┘

Tabla: lote_bodega (solo cambió la distribución)
┌────┬─────────┬────────────┬──────────┐
│ id │ id_lote │ id_bodega  │ cantidad │
├────┼─────────┼────────────┼──────────┤
│ 1  │ 1       │ 5 (Central)│ 1        │ ← Quedó 1
│ 2  │ 1       │ 7 (Sucur.) │ 2        │ ← Movieron 2
└────┴─────────┴────────────┴──────────┘
```

### Se asignan a 3 personas:

```
Tabla: lote (cantidad_disponible = 0, todas asignadas)
┌────┬────────────────────┬──────────────────┬──────────────────┬───────────┐
│ id │ id_producto        │ cantidad_disp    │ cantidad_inicial │ estado    │
├────┼────────────────────┼──────────────────┼──────────────────┼───────────┤
│ 1  │ LAPTOP-DELL-LAT    │ 0                │ 3                │ false     │
└────┴────────────────────┴──────────────────┴──────────────────┴───────────┘

Tabla: lote_bodega (sin stock en bodegas)
┌────┬─────────┬────────────┬──────────┐
│ id │ id_lote │ id_bodega  │ cantidad │
├────┼─────────┼────────────┼──────────┤
│ 1  │ 1       │ 5 (Central)│ 0        │
│ 2  │ 1       │ 7 (Sucur.) │ 0        │
└────┴─────────┴────────────┴──────────┘

Tabla: tarjeta_producto (3 registros)
┌────┬────────────┬──────────────┬─────────┐
│ id │ id_persona │ id_producto  │ id_lote │
├────┼────────────┼──────────────┼─────────┤
│ 1  │ 101        │ LAPTOP-DELL  │ 1       │
│ 2  │ 102        │ LAPTOP-DELL  │ 1       │
│ 3  │ 103        │ LAPTOP-DELL  │ 1       │
└────┴────────────┴──────────────┴─────────┘
```

### Nueva compra (20/03/2025):

```
Tabla: lote (NUEVO LOTE con su propia fecha/precio)
┌────┬────────────────────┬──────────────────┬──────────────────┬───────────┐
│ id │ id_producto        │ cantidad_disp    │ cantidad_inicial │ estado    │
├────┼────────────────────┼──────────────────┼──────────────────┼───────────┤
│ 1  │ LAPTOP-DELL-LAT    │ 0                │ 3                │ false     │
│ 2  │ LAPTOP-DELL-LAT    │ 3                │ 3                │ true      │ ← NUEVO
└────┴────────────────────┴──────────────────┴──────────────────┴───────────┘
```

**Resultado:** 2 lotes separados del mismo producto, cada uno con su historial.

---

## Índices de Rendimiento

### Índices Actualizados:

**Tabla `lote`:**
```sql
-- Índice FIFO actualizado (sin id_bodega)
CREATE INDEX idx_lote_fifo
ON lote(id_producto, cantidad_disponible, fecha_ingreso, estado);
```

**Tabla `lote_bodega`:**
```sql
-- Búsqueda rápida de ubicación
CREATE INDEX idx_lote_bodega_lookup ON lote_bodega(id_lote, id_bodega);

-- Listar lotes por bodega
CREATE INDEX idx_bodega_lotes ON lote_bodega(id_bodega);

-- Stock por lote
CREATE INDEX idx_lote_cantidad ON lote_bodega(id_lote, cantidad);

-- Stock disponible por bodega (con filtro WHERE)
CREATE INDEX idx_lote_bodega_stock ON lote_bodega(id_bodega, cantidad)
WHERE cantidad > 0;
```

---

## Instrucciones de Migración

### Paso 1: Hacer Backup

**IMPORTANTE:** Crear backup completo de la base de datos antes de migrar.

```bash
docker compose exec db mysqldump -u root -p eemq_db > backup_pre_refactor.sql
```

### Paso 2: Ejecutar Migraciones

```bash
docker compose exec app php artisan migrate
```

**Migraciones que se ejecutarán (en orden):**

1. `2025_11_25_000001_create_lote_bodega_table.php`
   - Crea la tabla `lote_bodega`

2. `2025_11_25_000002_refactor_lote_table.php`
   - Renombra `cantidad` → `cantidad_disponible`
   - Hace `id_bodega` nullable

3. `2025_11_25_000003_migrate_existing_lotes_to_lote_bodega.php`
   - Migra datos existentes a `lote_bodega`
   - **CRÍTICO:** Revisa los logs para verificar migración exitosa

4. `2025_11_25_000004_update_performance_indexes_for_lote_refactor.php`
   - Actualiza índices de rendimiento

### Paso 3: Verificar Migración

```bash
# Verificar que se creó lote_bodega
docker compose exec app php artisan tinker
>>> \App\Models\LoteBodega::count()

# Verificar que los datos se migraron correctamente
>>> \App\Models\Lote::first()->ubicaciones
```

### Paso 4: Pruebas Recomendadas

1. **Compra:** Crear una compra nueva y verificar que se crea el lote y su ubicación
2. **Traslado:** Trasladar productos entre bodegas y verificar que NO se crean lotes nuevos
3. **Requisición:** Crear requisición de consumibles y no consumibles
4. **Devolución:** Devolver productos y verificar que regresan al lote original
5. **Gestión Manual:** Crear/editar lotes desde el módulo de gestión de productos

---

## Rollback (Si es Necesario)

Si se encuentra algún problema crítico:

```bash
# Revertir las 4 migraciones
docker compose exec app php artisan migrate:rollback --step=4
```

**ADVERTENCIA:** El rollback puede causar pérdida de datos si se han creado lotes distribuidos en múltiples bodegas después de la migración.

---

## Compatibilidad hacia Atrás

### Código Legacy

El código antiguo que use `$lote->cantidad` seguirá funcionando gracias a los accessors/mutators:

```php
// Esto sigue funcionando
$cantidad = $lote->cantidad;  // Devuelve cantidad_disponible

// Esto también funciona
$lote->cantidad = 10;  // Actualiza cantidad_disponible
```

### Campo `id_bodega` en `lote`

Se mantiene temporalmente para compatibilidad, pero ya no es la fuente de verdad. En futuras versiones se puede eliminar completamente.

---

## Archivos Modificados

### Migraciones:
- ✅ `2025_11_25_000001_create_lote_bodega_table.php`
- ✅ `2025_11_25_000002_refactor_lote_table.php`
- ✅ `2025_11_25_000003_migrate_existing_lotes_to_lote_bodega.php`
- ✅ `2025_11_25_000004_update_performance_indexes_for_lote_refactor.php`

### Modelos:
- ✅ `app/Models/Lote.php` (actualizado)
- ✅ `app/Models/LoteBodega.php` (nuevo)

### Componentes Livewire:
- ✅ `app/Livewire/FormularioCompra.php`
- ✅ `app/Livewire/FormularioTraslado.php`
- ✅ `app/Livewire/FormularioRequisicion.php`
- ✅ `app/Livewire/FormularioDevolucion.php`
- ✅ `app/Livewire/GestionProductos.php`

### Vistas:
- ⚠️ Posiblemente se necesiten ajustes menores en las vistas blade para mostrar la distribución de lotes

---

## Notas Adicionales

### Performance

- Los índices creados optimizan las consultas FIFO
- La estructura `lote_bodega` es más eficiente que múltiples lotes
- Las consultas de stock son más rápidas con los nuevos índices

### Escalabilidad

- Un lote puede estar distribuido en N bodegas sin crear N lotes
- Facilita la trazabilidad completa de cada lote desde su compra
- Permite auditorías más precisas del inventario

### Futuras Mejoras

1. Eliminar completamente `id_bodega` de la tabla `lote`
2. Crear vistas especializadas para reportes de distribución
3. Agregar triggers para validaciones automáticas
4. Implementar soft deletes en `lote_bodega`

---

## Soporte

Si encuentras problemas durante la migración o tienes dudas sobre el nuevo sistema:

1. Revisar los logs: `storage/logs/laravel.log`
2. Verificar la migración de datos: comprobar que `lote_bodega` tenga registros
3. Revisar la integridad referencial de las foreign keys

---

**Fecha de Refactorización:** 25 de Noviembre, 2025
**Versión:** 1.0
**Estado:** ✅ Completado - Listo para migración
