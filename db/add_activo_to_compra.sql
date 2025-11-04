-- Script para agregar el campo 'activo' a la tabla 'compra'
-- Ejecutar este script manualmente en la base de datos

USE eemq_inventory;

-- Agregar columna 'activo' a la tabla 'compra'
ALTER TABLE `compra`
ADD COLUMN `activo` TINYINT(1) NOT NULL DEFAULT 1
AFTER `id_usuario`;

-- Actualizar todas las compras existentes como activas
UPDATE `compra` SET `activo` = 1 WHERE `activo` IS NULL OR `activo` = 0;
