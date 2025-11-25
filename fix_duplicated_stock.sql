-- Script para corregir datos duplicados por el bug de incrementarEnBodega
-- EJECUTAR ESTE SCRIPT ANTES DE HACER NUEVAS PRUEBAS

-- PASO 1: Respaldar datos actuales (opcional pero recomendado)
-- Ejecutar fuera de este script:
-- mysqldump -u root -p eemq_db > backup_antes_fix.sql

-- PASO 2: Corregir cantidad_disponible en tabla lote
-- La cantidad_disponible debe ser igual a la suma de cantidades en lote_bodega

UPDATE lote l
SET l.cantidad_disponible = (
    SELECT COALESCE(SUM(lb.cantidad), 0)
    FROM lote_bodega lb
    WHERE lb.id_lote = l.id
)
WHERE l.id > 0;

-- PASO 3: Verificar que cantidad_inicial no sea menor que cantidad_disponible
-- Si cantidad_disponible > cantidad_inicial, ajustar cantidad_inicial

UPDATE lote
SET cantidad_inicial = cantidad_disponible
WHERE cantidad_disponible > cantidad_inicial;

-- PASO 4: Mostrar resumen de correcciones
SELECT
    'Lotes corregidos' as tipo,
    COUNT(*) as total,
    SUM(cantidad_disponible) as suma_disponible,
    SUM(cantidad_inicial) as suma_inicial
FROM lote;

SELECT
    'Distribución en bodegas' as tipo,
    COUNT(*) as registros,
    SUM(cantidad) as suma_cantidades
FROM lote_bodega;

-- PASO 5: Verificar integridad
-- Mostrar lotes con posible inconsistencia (cantidad_disponible != suma de ubicaciones)
SELECT
    l.id,
    l.id_producto,
    l.cantidad_disponible,
    COALESCE(SUM(lb.cantidad), 0) as suma_en_bodegas,
    (l.cantidad_disponible - COALESCE(SUM(lb.cantidad), 0)) as diferencia
FROM lote l
LEFT JOIN lote_bodega lb ON l.id = lb.id_lote
GROUP BY l.id
HAVING ABS(diferencia) > 0;

-- Si el query anterior muestra filas, hay inconsistencias que deben revisarse manualmente
