CREATE TABLE `persona` (
  `id` int PRIMARY KEY,
  `nombres` varchar(255),
  `apellidos` varchar(255),
  `telefono` varchar(255),
  `correo` varchar(255),
  `fecha_nacimiento` date,
  `genero` varchar(255)
);

CREATE TABLE `usuario` (
  `id` int PRIMARY KEY,
  `nombre_usuario` varchar(255),
  `contrasena` varchar(255),
  `id_persona` int,
  `id_rol` int
);

CREATE TABLE `rol` (
  `id` int PRIMARY KEY,
  `nombre` varchar(255),
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
  `id_persona` int
);

CREATE TABLE `tarjeta_producto` (
  `id` int PRIMARY KEY,
  `costo_asignacion` double,
  `id_tarjeta` int,
  `id_producto` int
);

CREATE TABLE `producto` (
  `id` varchar(255) PRIMARY KEY,
  `nombre` varchar(255),
  `descripcion` varchar(255),
  `id_categoria` int
);

CREATE TABLE `categoria` (
  `id` int PRIMARY KEY,
  `nombre` varchar(255)
);

CREATE TABLE `lote` (
  `id` int PRIMARY KEY,
  `cantidad` int,
  `fecha_ingreso` datetime,
  `precio_ingreso` double,
  `observaciones` varchar(255),
  `id_producto` int,
  `id_bodega` int,
  `estado` bool
);

CREATE TABLE `bodega` (
  `id` int PRIMARY KEY,
  `nombre` varchar(255)
);

CREATE TABLE `proveedor` (
  `id` int PRIMARY KEY,
  `nit` varchar(255),
  `situacion_tributaria` int,
  `nombre` varchar(255),
  `id_usuario` int
);

CREATE TABLE `compra` (
  `id` int PRIMARY KEY,
  `fecha` datetime,
  `no_serie` varchar(255),
  `no_factura` varchar(255),
  `total` double,
  `id_proveedor` int
);

CREATE TABLE `detalle_compra` (
  `id` int PRIMARY KEY,
  `id_compra` int,
  `id_producto` int,
  `id_lote` int,
  `cantidad` int
);

CREATE TABLE `entrada` (
  `id` int PRIMARY KEY,
  `fecha` datetime,
  `total` double,
  `descripcion` varchar(255),
  `id_usuario` int
);

CREATE TABLE `detalle_entrada` (
  `id` int PRIMARY KEY,
  `id_entrada` int,
  `id_producto` int,
  `id_lote` int,
  `cantidad` int
);

CREATE TABLE `devolucion` (
  `id` int PRIMARY KEY,
  `fecha` datetime,
  `no_formulario` varchar(255),
  `foto` blob,
  `total` double,
  `id_usuario` int
);

CREATE TABLE `detalle_devolucion` (
  `id` int PRIMARY KEY,
  `id_devolucion` int,
  `id_producto` int,
  `id_lote` int,
  `cantidad` int
);

CREATE TABLE `traslado` (
  `id` int PRIMARY KEY,
  `fecha` datetime,
  `no_requisicion` varchar(255),
  `total` double,
  `descripcion` varchar(255),
  `id_usuario` int
);

CREATE TABLE `detalle_traslado` (
  `id` int PRIMARY KEY,
  `id_traslado` int,
  `id_producto` int,
  `id_lote` int,
  `id_tarjeta` int,
  `cantidad` int
);

CREATE TABLE `salida` (
  `id` int PRIMARY KEY,
  `fecha` datetime,
  `total` double,
  `descripcion` varchar(255),
  `id_usuario` int
);

CREATE TABLE `detalle_salida` (
  `id` int PRIMARY KEY,
  `id_salida` int,
  `id_producto` int,
  `id_lote` int,
  `id_tarjeta` int,
  `cantidad` int
);

CREATE TABLE `kardex` (
  `id` int PRIMARY KEY,
  `timestamp` datetime,
  `tipo_movimiento` varchar(255),
  `id_referencia` int
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

ALTER TABLE `detalle_salida` ADD FOREIGN KEY (`id_salida`) REFERENCES `salida` (`id`);

ALTER TABLE `detalle_salida` ADD FOREIGN KEY (`id_producto`) REFERENCES `producto` (`nombre`);

ALTER TABLE `entrada` ADD FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

ALTER TABLE `devolucion` ADD FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

ALTER TABLE `traslado` ADD FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

ALTER TABLE `salida` ADD FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

ALTER TABLE `detalle_compra` ADD FOREIGN KEY (`id_lote`) REFERENCES `lote` (`id`);

ALTER TABLE `detalle_entrada` ADD FOREIGN KEY (`id_lote`) REFERENCES `lote` (`id`);

ALTER TABLE `detalle_devolucion` ADD FOREIGN KEY (`id_lote`) REFERENCES `lote` (`id`);

ALTER TABLE `detalle_traslado` ADD FOREIGN KEY (`id_lote`) REFERENCES `lote` (`id`);

ALTER TABLE `detalle_salida` ADD FOREIGN KEY (`id_lote`) REFERENCES `lote` (`id`);

ALTER TABLE `permiso` ADD FOREIGN KEY (`id_configuracion`) REFERENCES `configuracion` (`id`);

ALTER TABLE `permiso` ADD FOREIGN KEY (`id_bitacora`) REFERENCES `bitacora` (`id`);

ALTER TABLE `detalle_traslado` ADD FOREIGN KEY (`id_tarjeta`) REFERENCES `tarjeta_responsabilidad` (`id`);

ALTER TABLE `detalle_salida` ADD FOREIGN KEY (`id_tarjeta`) REFERENCES `tarjeta_responsabilidad` (`id`);

ALTER TABLE `proveedor` ADD FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

ALTER TABLE `producto` ADD FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id`);

ALTER TABLE `lote` ADD FOREIGN KEY (`id_bodega`) REFERENCES `bodega` (`id`);

ALTER TABLE `usuario` ADD FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id`);

ALTER TABLE `rol` ADD FOREIGN KEY (`id_permiso`) REFERENCES `permiso` (`id`);

ALTER TABLE `kardex` ADD FOREIGN KEY (`id_referencia`) REFERENCES `detalle_compra` (`id`);

ALTER TABLE `kardex` ADD FOREIGN KEY (`id_referencia`) REFERENCES `detalle_entrada` (`id`);

ALTER TABLE `kardex` ADD FOREIGN KEY (`id_referencia`) REFERENCES `detalle_devolucion` (`id`);

ALTER TABLE `kardex` ADD FOREIGN KEY (`id_referencia`) REFERENCES `detalle_traslado` (`id`);

ALTER TABLE `kardex` ADD FOREIGN KEY (`id_referencia`) REFERENCES `detalle_salida` (`id`);
