-- ============================================================
-- SellSoft - Sistema ERP para Retail Multi-Sede
-- Base de Datos: sellsoft_db
-- Motor: MySQL 5.7+ con InnoDB
-- Zona horaria: America/Bogota (UTC-5)
-- Moneda: COP (Pesos Colombianos)
-- Versión: 1.0.0
-- Autor: SellSoft
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET time_zone = '-05:00';

-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS `sellsoft_db`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `sellsoft_db`;

-- ============================================================
-- TABLA: roles
-- Roles del sistema: administrador, vendedor, almacenero, invitado
-- ============================================================
CREATE TABLE IF NOT EXISTS `roles` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre`      VARCHAR(80) NOT NULL,
    `slug`        VARCHAR(80) NOT NULL,
    `descripcion` VARCHAR(255) DEFAULT NULL,
    `creado_en`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_roles_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Roles del sistema';

-- ============================================================
-- TABLA: permisos
-- Permisos granulares por módulo y acción
-- ============================================================
CREATE TABLE IF NOT EXISTS `permisos` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `modulo`      VARCHAR(80) NOT NULL COMMENT 'Ej: productos, ventas, reportes',
    `accion`      VARCHAR(80) NOT NULL COMMENT 'Ej: ver, crear, editar, eliminar',
    `descripcion` VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_permisos_modulo_accion` (`modulo`, `accion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Permisos granulares por módulo';

-- ============================================================
-- TABLA: rol_permisos
-- Relación muchos-a-muchos entre roles y permisos
-- ============================================================
CREATE TABLE IF NOT EXISTS `rol_permisos` (
    `rol_id`      INT UNSIGNED NOT NULL,
    `permiso_id`  INT UNSIGNED NOT NULL,
    PRIMARY KEY (`rol_id`, `permiso_id`),
    KEY `idx_rol_permisos_permiso` (`permiso_id`),
    CONSTRAINT `fk_rp_rol`    FOREIGN KEY (`rol_id`)     REFERENCES `roles`(`id`)    ON DELETE CASCADE,
    CONSTRAINT `fk_rp_permiso` FOREIGN KEY (`permiso_id`) REFERENCES `permisos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Asignación de permisos a roles';

-- ============================================================
-- TABLA: bodegas
-- Sedes / almacenes del negocio
-- ============================================================
CREATE TABLE IF NOT EXISTS `bodegas` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre`       VARCHAR(120) NOT NULL,
    `direccion`    VARCHAR(255) DEFAULT NULL,
    `telefono`     VARCHAR(20) DEFAULT NULL,
    `encargado_id` INT UNSIGNED DEFAULT NULL COMMENT 'FK a usuarios (se define después)',
    `horario`      VARCHAR(255) DEFAULT NULL COMMENT 'Ej: Lun-Vie 8am-6pm',
    `activo`       TINYINT(1) NOT NULL DEFAULT 1,
    `creado_en`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `actualizado_en` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Sedes o almacenes del negocio';

-- ============================================================
-- TABLA: usuarios
-- Usuarios del sistema con hash bcrypt
-- ============================================================
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre`           VARCHAR(120) NOT NULL,
    `correo`           VARCHAR(150) NOT NULL,
    `clave`            VARCHAR(255) NOT NULL COMMENT 'Hash bcrypt de password_hash()',
    `recuerdo_token`   VARCHAR(100) DEFAULT NULL COMMENT 'Token para recordar sesión',
    `bodega_id`        INT UNSIGNED DEFAULT NULL COMMENT 'Bodega predeterminada',
    `activo`           TINYINT(1) NOT NULL DEFAULT 1,
    `ultimo_acceso`    TIMESTAMP NULL DEFAULT NULL,
    `creado_en`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `actualizado_en`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_usuarios_correo` (`correo`),
    KEY `idx_usuarios_bodega` (`bodega_id`),
    CONSTRAINT `fk_usuarios_bodega` FOREIGN KEY (`bodega_id`) REFERENCES `bodegas`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Usuarios del sistema';

-- Ahora sí agregamos la FK de bodegas.encargado_id -> usuarios.id
ALTER TABLE `bodegas`
    ADD CONSTRAINT `fk_bodegas_encargado`
    FOREIGN KEY (`encargado_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL;

-- ============================================================
-- TABLA: usuario_roles
-- Un usuario puede tener múltiples roles
-- ============================================================
CREATE TABLE IF NOT EXISTS `usuario_roles` (
    `usuario_id` INT UNSIGNED NOT NULL,
    `rol_id`     INT UNSIGNED NOT NULL,
    PRIMARY KEY (`usuario_id`, `rol_id`),
    KEY `idx_usuario_roles_rol` (`rol_id`),
    CONSTRAINT `fk_ur_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ur_rol`     FOREIGN KEY (`rol_id`)     REFERENCES `roles`(`id`)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Asignación de roles a usuarios';

-- ============================================================
-- TABLA: categorias
-- Árbol ilimitado con auto-referencia (padre_id)
-- ============================================================
CREATE TABLE IF NOT EXISTS `categorias` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `padre_id`    INT UNSIGNED DEFAULT NULL COMMENT 'NULL = categoría raíz',
    `nombre`      VARCHAR(120) NOT NULL,
    `slug`        VARCHAR(150) NOT NULL,
    `imagen`      VARCHAR(255) DEFAULT NULL,
    `orden`       SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `activo`      TINYINT(1) NOT NULL DEFAULT 1,
    `creado_en`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_categorias_slug` (`slug`),
    KEY `idx_categorias_padre` (`padre_id`),
    CONSTRAINT `fk_categorias_padre` FOREIGN KEY (`padre_id`) REFERENCES `categorias`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Árbol de categorías y subcategorías';

-- ============================================================
-- TABLA: marcas
-- Marcas de los productos
-- ============================================================
CREATE TABLE IF NOT EXISTS `marcas` (
    `id`       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre`   VARCHAR(120) NOT NULL,
    `logo`     VARCHAR(255) DEFAULT NULL,
    `activo`   TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_marcas_nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Marcas de productos';

-- ============================================================
-- TABLA: proveedores
-- Proveedores de mercancía
-- ============================================================
CREATE TABLE IF NOT EXISTS `proveedores` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre`      VARCHAR(150) NOT NULL,
    `nit`         VARCHAR(20) DEFAULT NULL COMMENT 'NIT o cédula del proveedor',
    `correo`      VARCHAR(150) DEFAULT NULL,
    `telefono`    VARCHAR(20) DEFAULT NULL,
    `direccion`   VARCHAR(255) DEFAULT NULL,
    `contacto`    VARCHAR(120) DEFAULT NULL COMMENT 'Nombre del contacto',
    `activo`      TINYINT(1) NOT NULL DEFAULT 1,
    `creado_en`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `actualizado_en` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Proveedores de mercancía';

-- ============================================================
-- TABLA: tipos_producto
-- Tipos: físico, digital, servicio
-- ============================================================
CREATE TABLE IF NOT EXISTS `tipos_producto` (
    `id`     INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(80) NOT NULL,
    `slug`   VARCHAR(80) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_tipos_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Tipos de producto';

-- ============================================================
-- TABLA: productos
-- Catálogo principal de productos
-- ============================================================
CREATE TABLE IF NOT EXISTS `productos` (
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre`           VARCHAR(200) NOT NULL,
    `slug`             VARCHAR(220) NOT NULL,
    `descripcion`      TEXT DEFAULT NULL,
    `codigo_sku`       VARCHAR(80) DEFAULT NULL,
    `categoria_id`     INT UNSIGNED DEFAULT NULL,
    `marca_id`         INT UNSIGNED DEFAULT NULL,
    `proveedor_id`     INT UNSIGNED DEFAULT NULL,
    `tipo_id`          INT UNSIGNED DEFAULT NULL,
    `precio_compra`    DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `precio_venta`     DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `imagen_principal` VARCHAR(255) DEFAULT NULL,
    `peso`             DECIMAL(8,3) DEFAULT NULL COMMENT 'En kilogramos',
    `dimensiones`      VARCHAR(100) DEFAULT NULL COMMENT 'Ej: 10x5x3 cm',
    `activo`           TINYINT(1) NOT NULL DEFAULT 1,
    `destacado`        TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Mostrar en landing',
    `orden_posicion`   SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Para drag&drop',
    `creado_en`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `actualizado_en`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_productos_slug` (`slug`),
    UNIQUE KEY `uq_productos_sku` (`codigo_sku`),
    KEY `idx_productos_categoria` (`categoria_id`),
    KEY `idx_productos_marca` (`marca_id`),
    KEY `idx_productos_proveedor` (`proveedor_id`),
    KEY `idx_productos_tipo` (`tipo_id`),
    KEY `idx_productos_activo_destacado` (`activo`, `destacado`),
    CONSTRAINT `fk_prod_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_prod_marca`     FOREIGN KEY (`marca_id`)     REFERENCES `marcas`(`id`)     ON DELETE SET NULL,
    CONSTRAINT `fk_prod_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_prod_tipo`      FOREIGN KEY (`tipo_id`)      REFERENCES `tipos_producto`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Catálogo principal de productos';

-- ============================================================
-- TABLA: galeria_productos
-- Imágenes adicionales de cada producto
-- ============================================================
CREATE TABLE IF NOT EXISTS `galeria_productos` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `producto_id` INT UNSIGNED NOT NULL,
    `url_imagen`  VARCHAR(255) NOT NULL,
    `orden`       SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_galeria_producto` (`producto_id`),
    CONSTRAINT `fk_galeria_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Galería de imágenes por producto';

-- ============================================================
-- TABLA: atributos_productos
-- Atributos personalizables (talla, color, concentración, etc.)
-- ============================================================
CREATE TABLE IF NOT EXISTS `atributos_productos` (
    `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `producto_id`     INT UNSIGNED NOT NULL,
    `nombre_atributo` VARCHAR(100) NOT NULL COMMENT 'Ej: Concentración, Aroma, Talla',
    `valor_atributo`  VARCHAR(255) NOT NULL COMMENT 'Ej: EDP, Floral, XL',
    PRIMARY KEY (`id`),
    KEY `idx_atributos_producto` (`producto_id`),
    CONSTRAINT `fk_atrib_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Atributos personalizables de productos';

-- ============================================================
-- TABLA: producto_bodega
-- Stock diferenciado por sede/bodega
-- ============================================================
CREATE TABLE IF NOT EXISTS `producto_bodega` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `producto_id`  INT UNSIGNED NOT NULL,
    `bodega_id`    INT UNSIGNED NOT NULL,
    `stock_actual` INT NOT NULL DEFAULT 0,
    `stock_minimo` INT NOT NULL DEFAULT 0 COMMENT 'Nivel de alerta',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_prod_bodega` (`producto_id`, `bodega_id`),
    KEY `idx_pb_bodega` (`bodega_id`),
    CONSTRAINT `fk_pb_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pb_bodega`   FOREIGN KEY (`bodega_id`)   REFERENCES `bodegas`(`id`)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Stock de productos por bodega/sede';

-- ============================================================
-- TABLA: clientes
-- Base de datos de clientes
-- ============================================================
CREATE TABLE IF NOT EXISTS `clientes` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre`      VARCHAR(150) NOT NULL,
    `tipo_doc`    ENUM('CC','NIT','CE','PAS','NIT_EXT') NOT NULL DEFAULT 'CC'
                  COMMENT 'CC=Cédula, NIT=NIT, CE=Extranjería, PAS=Pasaporte',
    `numero_doc`  VARCHAR(20) DEFAULT NULL,
    `correo`      VARCHAR(150) DEFAULT NULL,
    `telefono`    VARCHAR(20) DEFAULT NULL,
    `direccion`   VARCHAR(255) DEFAULT NULL,
    `ciudad`      VARCHAR(100) DEFAULT NULL,
    `activo`      TINYINT(1) NOT NULL DEFAULT 1,
    `creado_en`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `actualizado_en` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_clientes_numero_doc` (`numero_doc`),
    KEY `idx_clientes_correo` (`correo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Clientes del negocio';

-- ============================================================
-- TABLA: ofertas
-- Promociones y descuentos
-- ============================================================
CREATE TABLE IF NOT EXISTS `ofertas` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre`        VARCHAR(150) NOT NULL,
    `tipo`          ENUM('porcentaje','monto_fijo','2x1','volumen') NOT NULL DEFAULT 'porcentaje',
    `valor`         DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Porcentaje o monto fijo',
    `alcance`       ENUM('producto','categoria','carrito') NOT NULL DEFAULT 'producto',
    `referencia_id` INT UNSIGNED DEFAULT NULL COMMENT 'ID del producto o categoría afectada',
    `fecha_inicio`  DATETIME DEFAULT NULL,
    `fecha_fin`     DATETIME DEFAULT NULL,
    `activo`        TINYINT(1) NOT NULL DEFAULT 1,
    `creado_en`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ofertas_activo_fechas` (`activo`, `fecha_inicio`, `fecha_fin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Promociones y descuentos';

-- ============================================================
-- TABLA: ventas
-- Registro maestro de ventas/cotizaciones
-- ============================================================
CREATE TABLE IF NOT EXISTS `ventas` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `codigo`         VARCHAR(20) NOT NULL COMMENT 'Ej: VTA-2024-000001',
    `usuario_id`     INT UNSIGNED NOT NULL COMMENT 'Vendedor',
    `cliente_id`     INT UNSIGNED DEFAULT NULL COMMENT 'NULL = consumidor final',
    `bodega_id`      INT UNSIGNED NOT NULL COMMENT 'Sede desde donde se vende',
    `subtotal`       DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `iva`            DECIMAL(14,2) NOT NULL DEFAULT 0.00 COMMENT 'IVA 19% Colombia',
    `descuento`      DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `total`          DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `metodo_pago`    ENUM('efectivo','tarjeta','transferencia','nequi','daviplata','otro') NOT NULL DEFAULT 'efectivo',
    `tipo`           ENUM('factura','cotizacion','nota_credito') NOT NULL DEFAULT 'factura',
    `estado`         ENUM('completada','anulada','pendiente') NOT NULL DEFAULT 'completada',
    `notas`          TEXT DEFAULT NULL,
    `revertido_por`  INT UNSIGNED DEFAULT NULL COMMENT 'Usuario que anuló la venta',
    `revertido_en`   TIMESTAMP NULL DEFAULT NULL,
    `creado_en`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `actualizado_en` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_ventas_codigo` (`codigo`),
    KEY `idx_ventas_usuario` (`usuario_id`),
    KEY `idx_ventas_cliente` (`cliente_id`),
    KEY `idx_ventas_bodega` (`bodega_id`),
    KEY `idx_ventas_estado` (`estado`),
    KEY `idx_ventas_creado` (`creado_en`),
    CONSTRAINT `fk_ventas_usuario`  FOREIGN KEY (`usuario_id`)    REFERENCES `usuarios`(`id`),
    CONSTRAINT `fk_ventas_cliente`  FOREIGN KEY (`cliente_id`)    REFERENCES `clientes`(`id`)  ON DELETE SET NULL,
    CONSTRAINT `fk_ventas_bodega`   FOREIGN KEY (`bodega_id`)     REFERENCES `bodegas`(`id`),
    CONSTRAINT `fk_ventas_revertido` FOREIGN KEY (`revertido_por`) REFERENCES `usuarios`(`id`)  ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Registro maestro de ventas';

-- ============================================================
-- TABLA: detalle_ventas
-- Líneas de cada venta
-- ============================================================
CREATE TABLE IF NOT EXISTS `detalle_ventas` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `venta_id`       INT UNSIGNED NOT NULL,
    `producto_id`    INT UNSIGNED NOT NULL,
    `bodega_id`      INT UNSIGNED NOT NULL,
    `cantidad`       INT NOT NULL DEFAULT 1,
    `precio_unitario` DECIMAL(14,2) NOT NULL,
    `descuento`      DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    `total`          DECIMAL(14,2) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_dv_venta` (`venta_id`),
    KEY `idx_dv_producto` (`producto_id`),
    CONSTRAINT `fk_dv_venta`    FOREIGN KEY (`venta_id`)    REFERENCES `ventas`(`id`)    ON DELETE CASCADE,
    CONSTRAINT `fk_dv_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos`(`id`),
    CONSTRAINT `fk_dv_bodega`   FOREIGN KEY (`bodega_id`)   REFERENCES `bodegas`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Detalle de líneas de venta';

-- ============================================================
-- TABLA: movimientos
-- Historial de movimientos de inventario
-- ============================================================
CREATE TABLE IF NOT EXISTS `movimientos` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `producto_id`   INT UNSIGNED NOT NULL,
    `bodega_id`     INT UNSIGNED NOT NULL,
    `usuario_id`    INT UNSIGNED NOT NULL,
    `tipo`          ENUM('entrada','salida') NOT NULL,
    `cantidad`      INT NOT NULL,
    `motivo`        ENUM('compra','venta','ajuste','devolucion','transferencia_entrada','transferencia_salida') NOT NULL,
    `referencia_id` INT UNSIGNED DEFAULT NULL COMMENT 'ID de venta o transferencia',
    `observacion`   VARCHAR(255) DEFAULT NULL,
    `creado_en`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_mov_producto` (`producto_id`),
    KEY `idx_mov_bodega` (`bodega_id`),
    KEY `idx_mov_usuario` (`usuario_id`),
    KEY `idx_mov_creado` (`creado_en`),
    CONSTRAINT `fk_mov_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos`(`id`),
    CONSTRAINT `fk_mov_bodega`   FOREIGN KEY (`bodega_id`)   REFERENCES `bodegas`(`id`),
    CONSTRAINT `fk_mov_usuario`  FOREIGN KEY (`usuario_id`)  REFERENCES `usuarios`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Historial de movimientos de inventario';

-- ============================================================
-- TABLA: transferencias
-- Traslados de stock entre sedes
-- ============================================================
CREATE TABLE IF NOT EXISTS `transferencias` (
    `id`                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `bodega_origen_id`    INT UNSIGNED NOT NULL,
    `bodega_destino_id`   INT UNSIGNED NOT NULL,
    `producto_id`         INT UNSIGNED NOT NULL,
    `cantidad`            INT NOT NULL,
    `estado`              ENUM('pendiente','autorizada','rechazada') NOT NULL DEFAULT 'pendiente',
    `solicitado_por`      INT UNSIGNED NOT NULL COMMENT 'Usuario que solicita',
    `autorizado_por`      INT UNSIGNED DEFAULT NULL COMMENT 'Admin que autoriza',
    `notas`               VARCHAR(255) DEFAULT NULL,
    `creado_en`           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `actualizado_en`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_trans_origen` (`bodega_origen_id`),
    KEY `idx_trans_destino` (`bodega_destino_id`),
    KEY `idx_trans_producto` (`producto_id`),
    CONSTRAINT `fk_trans_origen`    FOREIGN KEY (`bodega_origen_id`)  REFERENCES `bodegas`(`id`),
    CONSTRAINT `fk_trans_destino`   FOREIGN KEY (`bodega_destino_id`) REFERENCES `bodegas`(`id`),
    CONSTRAINT `fk_trans_producto`  FOREIGN KEY (`producto_id`)       REFERENCES `productos`(`id`),
    CONSTRAINT `fk_trans_solicitado` FOREIGN KEY (`solicitado_por`)   REFERENCES `usuarios`(`id`),
    CONSTRAINT `fk_trans_autorizado` FOREIGN KEY (`autorizado_por`)   REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Traslados de stock entre sedes';

-- ============================================================
-- TABLA: historial_correos
-- Registro de facturas enviadas por correo
-- ============================================================
CREATE TABLE IF NOT EXISTS `historial_correos` (
    `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `venta_id`        INT UNSIGNED DEFAULT NULL,
    `destinatarios`   TEXT NOT NULL COMMENT 'Correos separados por coma',
    `asunto`          VARCHAR(255) NOT NULL,
    `estado`          ENUM('enviado','fallido','pendiente') NOT NULL DEFAULT 'pendiente',
    `ruta_adjunto`    VARCHAR(255) DEFAULT NULL COMMENT 'Ruta del PDF adjunto',
    `enviado_en`      TIMESTAMP NULL DEFAULT NULL,
    `enviado_por`     INT UNSIGNED DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_hc_venta` (`venta_id`),
    KEY `idx_hc_enviado_por` (`enviado_por`),
    CONSTRAINT `fk_hc_venta`      FOREIGN KEY (`venta_id`)    REFERENCES `ventas`(`id`)    ON DELETE SET NULL,
    CONSTRAINT `fk_hc_enviado_por` FOREIGN KEY (`enviado_por`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Historial de facturas enviadas por correo';

-- ============================================================
-- TABLA: configuracion
-- Ajustes globales del sistema
-- ============================================================
CREATE TABLE IF NOT EXISTS `configuracion` (
    `id`     INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `clave`  VARCHAR(100) NOT NULL,
    `valor`  TEXT DEFAULT NULL,
    `grupo`  VARCHAR(80) NOT NULL DEFAULT 'general' COMMENT 'Ej: general, factura, correo, moneda',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_config_clave` (`clave`),
    KEY `idx_config_grupo` (`grupo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Configuración general del sistema';

SET FOREIGN_KEY_CHECKS = 1;
