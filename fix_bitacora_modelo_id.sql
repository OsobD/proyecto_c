-- Migration: Modify bitacora.modelo_id to support string IDs
-- This allows the bitacora table to store alphanumeric IDs like 'eje-3' for products

-- Drop the composite index first
ALTER TABLE `bitacora` DROP INDEX `bitacora_modelo_modelo_id_index`;

-- Change modelo_id from BIGINT UNSIGNED to VARCHAR(255)
ALTER TABLE `bitacora` MODIFY COLUMN `modelo_id` VARCHAR(255) NULL;

-- Recreate the composite index
ALTER TABLE `bitacora` ADD INDEX `bitacora_modelo_modelo_id_index` (`modelo`, `modelo_id`);
