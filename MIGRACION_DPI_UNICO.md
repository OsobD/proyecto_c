# Migración: Índice Único para DPI

## Descripción
Esta migración agrega un índice único al campo `dpi` en la tabla `persona` para garantizar que no puedan existir dos personas con el mismo número de Documento Personal de Identificación (DPI).

## Archivo de Migración
`database/migrations/2025_11_18_065754_add_unique_index_to_dpi_in_persona_table.php`

## Antes de Ejecutar

### 1. Verificar DPIs Duplicados
Antes de ejecutar la migración, verifica si existen DPIs duplicados en la base de datos:

```sql
SELECT dpi, COUNT(*) as cantidad
FROM persona
WHERE dpi IS NOT NULL
GROUP BY dpi
HAVING cantidad > 1;
```

### 2. Limpiar Duplicados (si existen)
Si hay duplicados, deberás decidir cuál registro mantener y actualizar o eliminar los demás:

```sql
-- Ver detalles de los duplicados
SELECT * FROM persona WHERE dpi = 'NUMERO_DPI_DUPLICADO';

-- Opción 1: Actualizar el DPI duplicado
UPDATE persona SET dpi = 'NUEVO_DPI' WHERE id = ID_PERSONA_DUPLICADA;

-- Opción 2: Eliminar la persona duplicada (¡CUIDADO!)
DELETE FROM persona WHERE id = ID_PERSONA_DUPLICADA;
```

## Ejecutar la Migración

```bash
php artisan migrate
```

Si estás en producción:

```bash
php artisan migrate --force
```

## Validaciones Implementadas

### A Nivel de Aplicación (Laravel)
- ✅ `ModalPersona.php`: Validación `unique:persona,dpi`
- ✅ `GestionPersonas.php`: Validación `unique:persona,dpi` (con excepción en edición)

### A Nivel de Base de Datos
- ✅ Índice único `persona_dpi_unique` en la columna `dpi`

## Mensajes de Error

Cuando un usuario intenta registrar un DPI que ya existe, verá:

> **"Ya existe una persona registrada con este DPI. El DPI debe ser único."**

## Reversión

Si necesitas revertir esta migración:

```bash
php artisan migrate:rollback --step=1
```

**Nota:** Esto eliminará el índice único, permitiendo nuevamente DPIs duplicados.

## Protección Contra Race Conditions

El índice único en la base de datos protege contra condiciones de carrera donde dos usuarios podrían intentar crear personas con el mismo DPI simultáneamente. Sin el índice, ambas validaciones de Laravel podrían pasar al mismo tiempo, resultando en duplicados.

Con el índice:
1. La validación de Laravel verifica primero
2. Si pasa, intenta insertar en la base de datos
3. Si otro usuario insertó el mismo DPI entre la validación y la inserción, la base de datos rechazará la operación con un error de constraint

## Mantenimiento

Una vez ejecutada esta migración:
- ✅ No será posible crear personas con DPIs duplicados
- ✅ Los formularios mostrarán mensajes de error claros
- ✅ La integridad de datos está garantizada a nivel de base de datos
