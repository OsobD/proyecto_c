CREATE TABLE `persona` (
  `id` int PRIMARY KEY,
  `nombres` varchar(255),
  `apellidos` varchar(255),
  `dpi` int,
  `telefono` varchar(255),
  `correo` varchar(255),
  `fecha_nacimiento` date,
  `genero` varchar(255),
  `estado` bool
);

CREATE TABLE `usuario` (
  `id` int PRIMARY KEY,
  `nombre_usuario` varchar(255),
  `contrasena` varchar(255),
  `id_persona` int,
  `id_rol` int,
  `estado` bool
);

CREATE TABLE `rol` (
  `id` int PRIMARY KEY,
  `nombre` varchar(255),
  `id_permiso` int
);

CREATE TABLE `rol_permiso` (
  `id` int PRIMARY KEY,
  `id_rol` int,
  `id_permiso` int
);

CREATE TABLE `permiso` (
  `id` int PRIMARY KEY,
  `nombre` varchar(255),
  `id_configuracion` int,
  `id_bitacora` int
);

CREATE TABLE `configuracion` (
  `id` int PRIMARY KEY,
  `nombre` varchar(255)
);

CREATE TABLE `bitacora` (
  `id` int PRIMARY KEY
);

CREATE TABLE `tarjeta_responsabilidad` (
  `id` int PRIMARY KEY,
  `fecha_creacion` datetime,
  `total` double,
  `id_persona` int,
  `estado` bool
);

CREATE TABLE `tarjeta_producto` (
  `id` int PRIMARY KEY,
  `precio_asignacion` double,
  `id_tarjeta` int,
  `id_producto` varchar(255),
  `id_lote` int
);

CREATE TABLE `producto` (
  `id` varchar(255) PRIMARY KEY,
  `descripcion` varchar(255),
  `id_categoria` int,
  `es_consumible` bool
);

CREATE TABLE `categoria` (
  `id` int PRIMARY KEY,
  `nombre` varchar(255)
);

CREATE TABLE `lote` (
  `id` int PRIMARY KEY,
  `cantidad` int,
  `cantidad_inicial` int,
  `fecha_ingreso` datetime,
  `precio_ingreso` double,
  `observaciones` varchar(255),
  `id_producto` varchar(255),
  `id_bodega` int,
  `estado` bool,
  `id_transaccion` int
);

CREATE TABLE `transaccion` (
  `id` int PRIMARY KEY,
  `id_tipo` int,
  `id_compra` int,
  `id_entrada` int,
  `id_devolucion` int,
  `id_traslado` int,
  `id_salida` int
);

CREATE TABLE `tipo_transacion` (
  `id_tipo` int PRIMARY KEY,
  `nombre` varchar(255)
);

CREATE TABLE `bodega` (
  `id` int PRIMARY KEY,
  `nombre` varchar(255)
);

CREATE TABLE `regimen_tributario` (
  `id` int PRIMARY KEY,
  `nombre` varchar(255)
);

CREATE TABLE `proveedor` (
  `id` int PRIMARY KEY,
  `nit` varchar(255),
  `id_regimen` int,
  `nombre` varchar(255),
  `estado` bool
);

CREATE TABLE `compra` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `fecha` datetime,
  `no_factura` varchar(255),
  `no_serie` varchar(255),
  `correlativo` int,
  `total` double,
  `precio_factura` double COMMENT 'Precio total según factura física para verificación',
  `id_proveedor` int,
  `id_bodega` int,
  `id_usuario` int,
  `activo` bool DEFAULT true
);

CREATE TABLE `detalle_compra` (
  `id` int PRIMARY KEY,
  `id_compra` int,
  `id_producto` varchar(255),
  `precio_ingreso` double,
  `cantidad` int
);

CREATE TABLE `entrada` (
  `id` int PRIMARY KEY,
  `fecha` datetime,
  `total` double,
  `descripcion` varchar(255),
  `id_usuario` int,
  `id_tipo` int,
  `id_tarjeta` int,
  `id_bodega` int
);

CREATE TABLE `detalle_entrada` (
  `id` int PRIMARY KEY,
  `id_entrada` int,
  `id_producto` varchar(255),
  `cantidad` int,
  `precio_ingreso` decimal
);

CREATE TABLE `tipo_entrada` (
  `id` int PRIMARY KEY,
  `nombre` varchar(255)
);

CREATE TABLE `devolucion` (
  `id` int PRIMARY KEY,
  `fecha` datetime,
  `no_formulario` varchar(255),
  `foto` blob,
  `total` double,
  `id_usuario` int,
  `id_tarjeta` int,
  `id_bodega` int,
  `id_traslado` int
);

CREATE TABLE `detalle_devolucion` (
  `id` int PRIMARY KEY,
  `id_devolucion` int,
  `id_producto` varchar(255),
  `id_lote` int,
  `cantidad` int
);

CREATE TABLE `traslado` (
  `id` int PRIMARY KEY,
  `fecha` datetime,
  `no_requisicion` varchar(255),
  `total` double,
  `descripcion` varchar(255),
  `id_usuario` int,
  `id_bodega` int,
  `id_tarjeta` int
);

CREATE TABLE `detalle_traslado` (
  `id` int PRIMARY KEY,
  `id_traslado` int,
  `id_producto` varchar(255),
  `cantidad` int,
  `id_lote` int,
  `precio_traslado` double
);

CREATE TABLE `salida` (
  `id` int PRIMARY KEY,
  `fecha` datetime,
  `total` double,
  `descripcion` varchar(255),
  `ubicacion` varchar(255),
  `id_tipo` int,
  `id_usuario` int,
  `id_persona` int,
  `id_tarjeta` int,
  `id_bodega` int
);

CREATE TABLE `tipo_salida` (
  `id` int PRIMARY KEY,
  `nombre` varchar(255)
);

CREATE TABLE `detalle_salida` (
  `id` int PRIMARY KEY,
  `id_salida` int,
  `id_producto` varchar(255),
  `id_lote` int,
  `cantidad` int,
  `precio_salida` decimal
);

CREATE TABLE `kardex` (
  `id` int PRIMARY KEY,
  `timestamp` datetime,
  `tipo_movimiento` varchar(255),
  `id_detalle` int
);

CREATE TABLE `detalle` (
  `id` int PRIMARY KEY,
  `id_tipo` int,
  `id_det_compra` int,
  `id_det_entrada` int,
  `id_det_devolucion` int,
  `id_det_traslado` int,
  `id_det_salida` int
);

ALTER TABLE `usuario` ADD FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id`);

ALTER TABLE `tarjeta_responsabilidad` ADD FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id`);

ALTER TABLE `tarjeta_producto` ADD FOREIGN KEY (`id_tarjeta`) REFERENCES `tarjeta_responsabilidad` (`id`);

ALTER TABLE `tarjeta_producto` ADD FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`);

ALTER TABLE `lote` ADD FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`);

ALTER TABLE `compra` ADD FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id`);

ALTER TABLE `detalle_compra` ADD FOREIGN KEY (`id_compra`) REFERENCES `compra` (`id`);

ALTER TABLE `detalle_compra` ADD FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`);

ALTER TABLE `detalle_entrada` ADD FOREIGN KEY (`id_entrada`) REFERENCES `entrada` (`id`);

ALTER TABLE `detalle_entrada` ADD FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`);

ALTER TABLE `detalle_devolucion` ADD FOREIGN KEY (`id_devolucion`) REFERENCES `devolucion` (`id`);

ALTER TABLE `detalle_devolucion` ADD FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`);

ALTER TABLE `detalle_traslado` ADD FOREIGN KEY (`id_traslado`) REFERENCES `traslado` (`id`);

ALTER TABLE `detalle_traslado` ADD FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`);

ALTER TABLE `detalle_salida` ADD FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`);

ALTER TABLE `entrada` ADD FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

ALTER TABLE `devolucion` ADD FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

ALTER TABLE `traslado` ADD FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

ALTER TABLE `salida` ADD FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

ALTER TABLE `detalle_devolucion` ADD FOREIGN KEY (`id_lote`) REFERENCES `lote` (`id`);

ALTER TABLE `detalle_salida` ADD FOREIGN KEY (`id_lote`) REFERENCES `lote` (`id`);

ALTER TABLE `permiso` ADD FOREIGN KEY (`id_configuracion`) REFERENCES `configuracion` (`id`);

ALTER TABLE `permiso` ADD FOREIGN KEY (`id_bitacora`) REFERENCES `bitacora` (`id`);

ALTER TABLE `producto` ADD FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id`);

ALTER TABLE `lote` ADD FOREIGN KEY (`id_bodega`) REFERENCES `bodega` (`id`);

ALTER TABLE `usuario` ADD FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id`);

ALTER TABLE `proveedor` ADD FOREIGN KEY (`id_regimen`) REFERENCES `regimen_tributario` (`id`);

ALTER TABLE `compra` ADD FOREIGN KEY (`id_bodega`) REFERENCES `bodega` (`id`);

ALTER TABLE `compra` ADD FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

ALTER TABLE `tarjeta_producto` ADD FOREIGN KEY (`id_lote`) REFERENCES `lote` (`id`);

ALTER TABLE `entrada` ADD FOREIGN KEY (`id_bodega`) REFERENCES `bodega` (`id`);

ALTER TABLE `transaccion` ADD FOREIGN KEY (`id_compra`) REFERENCES `compra` (`id`);

ALTER TABLE `transaccion` ADD FOREIGN KEY (`id_entrada`) REFERENCES `entrada` (`id`);

ALTER TABLE `transaccion` ADD FOREIGN KEY (`id_devolucion`) REFERENCES `devolucion` (`id`);

ALTER TABLE `transaccion` ADD FOREIGN KEY (`id_traslado`) REFERENCES `traslado` (`id`);

ALTER TABLE `transaccion` ADD FOREIGN KEY (`id_tipo`) REFERENCES `tipo_transacion` (`id_tipo`);

ALTER TABLE `lote` ADD FOREIGN KEY (`id_transaccion`) REFERENCES `transaccion` (`id`);

ALTER TABLE `salida` ADD FOREIGN KEY (`id_tarjeta`) REFERENCES `tarjeta_producto` (`id`);

ALTER TABLE `traslado` ADD FOREIGN KEY (`id_bodega`) REFERENCES `bodega` (`id`);

ALTER TABLE `traslado` ADD FOREIGN KEY (`id_tarjeta`) REFERENCES `tarjeta_producto` (`id`);

ALTER TABLE `detalle` ADD FOREIGN KEY (`id_tipo`) REFERENCES `tipo_transacion` (`id_tipo`);

ALTER TABLE `detalle` ADD FOREIGN KEY (`id_det_compra`) REFERENCES `detalle_compra` (`id`);

ALTER TABLE `detalle` ADD FOREIGN KEY (`id_det_entrada`) REFERENCES `detalle_entrada` (`id`);

ALTER TABLE `detalle` ADD FOREIGN KEY (`id_det_devolucion`) REFERENCES `detalle_devolucion` (`id`);

ALTER TABLE `detalle` ADD FOREIGN KEY (`id_det_traslado`) REFERENCES `detalle_traslado` (`id`);

ALTER TABLE `detalle` ADD FOREIGN KEY (`id_det_salida`) REFERENCES `detalle_salida` (`id`);

ALTER TABLE `kardex` ADD FOREIGN KEY (`id_detalle`) REFERENCES `detalle` (`id`);

ALTER TABLE `devolucion` ADD FOREIGN KEY (`id_tarjeta`) REFERENCES `tarjeta_producto` (`id`);

ALTER TABLE `devolucion` ADD FOREIGN KEY (`id_bodega`) REFERENCES `bodega` (`id`);

ALTER TABLE `detalle_traslado` ADD FOREIGN KEY (`id_lote`) REFERENCES `lote` (`id`);

ALTER TABLE `devolucion` ADD FOREIGN KEY (`id_traslado`) REFERENCES `traslado` (`id`);

ALTER TABLE `salida` ADD FOREIGN KEY (`id_bodega`) REFERENCES `bodega` (`id`);

ALTER TABLE `entrada` ADD FOREIGN KEY (`id_tipo`) REFERENCES `tipo_entrada` (`id`);

ALTER TABLE `transaccion` ADD FOREIGN KEY (`id_salida`) REFERENCES `salida` (`id`);

ALTER TABLE `salida` ADD FOREIGN KEY (`id_tipo`) REFERENCES `tipo_salida` (`id`);

ALTER TABLE `salida` ADD FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id`);

ALTER TABLE `rol_permiso` ADD FOREIGN KEY (`id_permiso`) REFERENCES `permiso` (`id`);

ALTER TABLE `rol_permiso` ADD FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id`);

ALTER TABLE `entrada` ADD FOREIGN KEY (`id_tarjeta`) REFERENCES `tarjeta_producto` (`id`);

ALTER TABLE `detalle_salida` ADD FOREIGN KEY (`id_salida`) REFERENCES `salida` (`id`);
