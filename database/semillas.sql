-- ============================================================
-- SellSoft - Datos de Prueba (Semillas)
-- ============================================================

USE `sellsoft_db`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- ROLES
-- ============================================================
INSERT INTO `roles` (`id`, `nombre`, `slug`, `descripcion`) VALUES
(1, 'Administrador', 'administrador', 'Acceso total al sistema'),
(2, 'Vendedor',      'vendedor',      'Puede realizar ventas y consultar productos'),
(3, 'Almacenero',   'almacenero',    'Gestión de inventario y bodegas'),
(4, 'Invitado',     'invitado',      'Solo lectura en catálogo de productos');

-- ============================================================
-- PERMISOS
-- ============================================================
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('panel',          'ver',      'Ver el panel de control'),
('productos',      'ver',      'Ver listado de productos'),
('productos',      'crear',    'Crear nuevos productos'),
('productos',      'editar',   'Editar productos existentes'),
('productos',      'eliminar', 'Eliminar productos'),
('categorias',     'ver',      'Ver categorías'),
('categorias',     'crear',    'Crear categorías'),
('categorias',     'editar',   'Editar categorías'),
('categorias',     'eliminar', 'Eliminar categorías'),
('ventas',         'ver',      'Ver listado de ventas'),
('ventas',         'crear',    'Registrar ventas (POS)'),
('ventas',         'anular',   'Anular ventas con PIN'),
('inventario',     'ver',      'Ver movimientos de inventario'),
('inventario',     'ajustar',  'Realizar ajustes de inventario'),
('reportes',       'ver',      'Ver reportes e informes'),
('reportes',       'exportar', 'Exportar reportes a Excel/PDF'),
('clientes',       'ver',      'Ver clientes'),
('clientes',       'crear',    'Crear clientes'),
('clientes',       'editar',   'Editar clientes'),
('proveedores',    'ver',      'Ver proveedores'),
('proveedores',    'crear',    'Crear proveedores'),
('bodegas',        'ver',      'Ver bodegas/sedes'),
('bodegas',        'administrar', 'Crear y editar bodegas'),
('transferencias', 'solicitar', 'Solicitar transferencias entre sedes'),
('transferencias', 'autorizar', 'Autorizar transferencias (solo admin)'),
('usuarios',       'ver',      'Ver usuarios'),
('usuarios',       'administrar', 'Crear, editar y desactivar usuarios'),
('configuracion',  'ver',      'Ver configuración'),
('configuracion',  'editar',   'Modificar configuración del sistema');

-- Asignar TODOS los permisos al rol Administrador
INSERT INTO `rol_permisos` (`rol_id`, `permiso_id`)
SELECT 1, `id` FROM `permisos`;

-- Permisos del Vendedor
INSERT INTO `rol_permisos` (`rol_id`, `permiso_id`)
SELECT 2, `id` FROM `permisos`
WHERE `modulo` IN ('panel','productos','ventas','clientes')
AND `accion` IN ('ver','crear');

-- Permisos del Almacenero
INSERT INTO `rol_permisos` (`rol_id`, `permiso_id`)
SELECT 3, `id` FROM `permisos`
WHERE `modulo` IN ('panel','productos','inventario','bodegas','transferencias')
AND `accion` IN ('ver','ajustar','solicitar');

-- Permisos del Invitado (solo ver productos)
INSERT INTO `rol_permisos` (`rol_id`, `permiso_id`)
SELECT 4, `id` FROM `permisos`
WHERE `modulo` = 'productos' AND `accion` = 'ver';

-- ============================================================
-- BODEGAS (Sedes de prueba - Perfumería)
-- ============================================================
INSERT INTO `bodegas` (`id`, `nombre`, `direccion`, `telefono`, `horario`, `activo`) VALUES
(1, 'Sede Principal - Centro', 'Calle 15 #8-32, Bogotá D.C.', '601-234-5678', 'Lun-Sáb 9am-7pm', 1),
(2, 'Sede Norte - Usaquén',    'Cra 15 #120-45, Bogotá D.C.', '601-876-5432', 'Lun-Dom 10am-8pm', 1),
(3, 'Sede Sur - Kennedy',      'Av. Américas #40-22, Bogotá D.C.', '601-555-9988', 'Lun-Sáb 9am-6pm', 1);

-- ============================================================
-- USUARIOS (Contraseña para todos: Admin2024!)
-- Hash generado con password_hash('Admin2024!', PASSWORD_BCRYPT)
-- ============================================================
INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `clave`, `bodega_id`, `activo`) VALUES
(1, 'Administrador Sistema', 'admin@sellsoft.co',     '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1),
(2, 'María Vendedora',       'vendedor@sellsoft.co',  '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1),
(3, 'Carlos Almacenero',     'almacen@sellsoft.co',   '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 1),
(4, 'Invitado Demo',         'invitado@sellsoft.co',  '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1);

-- Actualizar encargados de bodegas
UPDATE `bodegas` SET `encargado_id` = 1 WHERE `id` = 1;
UPDATE `bodegas` SET `encargado_id` = 3 WHERE `id` = 2;

-- Asignar roles a usuarios
INSERT INTO `usuario_roles` (`usuario_id`, `rol_id`) VALUES
(1, 1), -- admin -> Administrador
(2, 2), -- maria -> Vendedor
(3, 3), -- carlos -> Almacenero
(4, 4); -- invitado -> Invitado

-- ============================================================
-- TIPOS DE PRODUCTO
-- ============================================================
INSERT INTO `tipos_producto` (`id`, `nombre`, `slug`) VALUES
(1, 'Físico',   'fisico'),
(2, 'Digital',  'digital'),
(3, 'Servicio', 'servicio');

-- ============================================================
-- MARCAS
-- ============================================================
INSERT INTO `marcas` (`id`, `nombre`, `activo`) VALUES
(1, 'Dior',           1),
(2, 'Chanel',         1),
(3, 'Calvin Klein',   1),
(4, 'Hugo Boss',      1),
(5, 'Versace',        1),
(6, 'Marca Propia',   1);

-- ============================================================
-- CATEGORIAS (árbol)
-- ============================================================
INSERT INTO `categorias` (`id`, `padre_id`, `nombre`, `slug`, `orden`, `activo`) VALUES
(1,  NULL, 'Perfumes',              'perfumes',              1, 1),
(2,  NULL, 'Cuidado Personal',      'cuidado-personal',      2, 1),
(3,  NULL, 'Accesorios',            'accesorios',            3, 1),
(4,  1,   'Perfumes Mujer',         'perfumes-mujer',        1, 1),
(5,  1,   'Perfumes Hombre',        'perfumes-hombre',       2, 1),
(6,  1,   'Perfumes Unisex',        'perfumes-unisex',       3, 1),
(7,  2,   'Cremas y Lociones',      'cremas-lociones',       1, 1),
(8,  2,   'Desodorantes',           'desodorantes',          2, 1),
(9,  3,   'Estuches de Regalo',     'estuches-regalo',       1, 1);

-- ============================================================
-- PROVEEDORES
-- ============================================================
INSERT INTO `proveedores` (`id`, `nombre`, `nit`, `correo`, `telefono`, `contacto`, `activo`) VALUES
(1, 'Distribuidora Fragancias Colombia', '900123456-7', 'compras@fragcol.co',  '601-300-1234', 'Andrés Mora',    1),
(2, 'Importadora Luxury Scents',         '901234567-8', 'pedidos@luxscents.co', '601-400-5678', 'Patricia Ruiz',  1),
(3, 'Proveedor Local Bogotá',            '800123456-1', 'ventas@provelocal.co','3001234567',   'Juan González',  1);

-- ============================================================
-- PRODUCTOS DE PRUEBA
-- ============================================================
INSERT INTO `productos` (`id`, `nombre`, `slug`, `descripcion`, `codigo_sku`, `categoria_id`, `marca_id`, `proveedor_id`, `tipo_id`, `precio_compra`, `precio_venta`, `activo`, `destacado`, `orden_posicion`) VALUES
(1, 'Miss Dior Blooming Bouquet 100ml', 'miss-dior-blooming-bouquet-100ml',
   'Fragancia floral fresca con notas de peonía, mandarina y almizcle blanco.',
   'DIO-MBB-100', 4, 1, 1, 1, 180000.00, 320000.00, 1, 1, 1),

(2, 'Chanel N°5 Eau de Parfum 50ml', 'chanel-no5-edp-50ml',
   'El perfume más famoso del mundo. Notas de aldehídos, rosa, jazmín y sándalo.',
   'CHA-N5-050', 4, 2, 2, 1, 250000.00, 450000.00, 1, 1, 2),

(3, 'Calvin Klein Eternity Men 100ml', 'calvin-klein-eternity-men-100ml',
   'Fragancia masculina clásica con notas de lavanda, menta y sándalo.',
   'CKL-ETM-100', 5, 3, 1, 1, 120000.00, 210000.00, 1, 1, 3),

(4, 'Hugo Boss Bottled EDT 100ml', 'hugo-boss-bottled-edt-100ml',
   'Fragancia icónica masculina con manzana, canela y madera de cedro.',
   'HUG-BOT-100', 5, 4, 3, 1, 140000.00, 240000.00, 1, 0, 4),

(5, 'Versace Bright Crystal 90ml', 'versace-bright-crystal-90ml',
   'Perfume fresco y sensual para mujer con notas de granada y peonía.',
   'VER-BRC-090', 4, 5, 2, 1, 160000.00, 280000.00, 1, 1, 5),

(6, 'Crema Hidratante Corporal 300ml', 'crema-hidratante-corporal-300ml',
   'Crema hidratante de uso diario con vitamina E y aceite de argán.',
   'CUI-CHI-300', 7, 6, 3, 1, 15000.00, 35000.00, 1, 0, 6);

-- ============================================================
-- ATRIBUTOS DE PRODUCTOS
-- ============================================================
INSERT INTO `atributos_productos` (`producto_id`, `nombre_atributo`, `valor_atributo`) VALUES
(1, 'Concentración', 'EDT (Eau de Toilette)'),
(1, 'Familia Olfativa', 'Floral'),
(1, 'Volumen', '100 ml'),
(2, 'Concentración', 'EDP (Eau de Parfum)'),
(2, 'Familia Olfativa', 'Floral Aldehídico'),
(2, 'Volumen', '50 ml'),
(3, 'Concentración', 'EDP (Eau de Parfum)'),
(3, 'Familia Olfativa', 'Aromático Fougère'),
(3, 'Volumen', '100 ml'),
(4, 'Concentración', 'EDT (Eau de Toilette)'),
(4, 'Familia Olfativa', 'Aromático'),
(4, 'Volumen', '100 ml'),
(5, 'Concentración', 'EDT (Eau de Toilette)'),
(5, 'Familia Olfativa', 'Floral Frutal'),
(5, 'Volumen', '90 ml');

-- ============================================================
-- STOCK POR BODEGA
-- ============================================================
INSERT INTO `producto_bodega` (`producto_id`, `bodega_id`, `stock_actual`, `stock_minimo`) VALUES
-- Sede Principal
(1, 1, 15, 3), (2, 1, 8,  2), (3, 1, 20, 5), (4, 1, 12, 3), (5, 1, 10, 2), (6, 1, 30, 5),
-- Sede Norte
(1, 2, 10, 2), (2, 2, 5,  2), (3, 2, 12, 3), (4, 2, 8,  2), (5, 2, 7,  2), (6, 2, 20, 5),
-- Sede Sur
(1, 3, 6,  2), (2, 3, 3,  2), (3, 3, 8,  2), (4, 3, 5,  2), (5, 3, 4,  2), (6, 3, 15, 5);

-- ============================================================
-- CLIENTES DE PRUEBA
-- ============================================================
INSERT INTO `clientes` (`id`, `nombre`, `tipo_doc`, `numero_doc`, `correo`, `telefono`, `ciudad`, `activo`) VALUES
(1, 'Consumidor Final',    'CC',  '',           '',                      '',            'Bogotá', 1),
(2, 'Luisa Fernanda Ríos', 'CC',  '1020304050', 'luisa@ejemplo.co',      '3151234567',  'Bogotá', 1),
(3, 'Empresa ABC S.A.S',   'NIT', '900555888-1','compras@empresaabc.co', '6013456789',  'Bogotá', 1),
(4, 'Pedro Martínez',      'CC',  '80123456',   'pedro@correo.co',       '3209876543',  'Medellín', 1);

-- ============================================================
-- VENTAS DE PRUEBA
-- ============================================================
INSERT INTO `ventas` (`id`, `codigo`, `usuario_id`, `cliente_id`, `bodega_id`, `subtotal`, `iva`, `descuento`, `total`, `metodo_pago`, `tipo`, `estado`, `creado_en`) VALUES
(1, 'VTA-2024-000001', 2, 2, 1, 268907.56, 51092.44, 0.00, 320000.00, 'efectivo', 'factura', 'completada', NOW() - INTERVAL 2 DAY),
(2, 'VTA-2024-000002', 2, 3, 1, 378151.26, 71848.74, 0.00, 450000.00, 'tarjeta',  'factura', 'completada', NOW() - INTERVAL 1 DAY),
(3, 'VTA-2024-000003', 2, 1, 1, 176470.59, 33529.41, 0.00, 210000.00, 'nequi',    'factura', 'completada', NOW());

INSERT INTO `detalle_ventas` (`venta_id`, `producto_id`, `bodega_id`, `cantidad`, `precio_unitario`, `descuento`, `total`) VALUES
(1, 1, 1, 1, 320000.00, 0.00, 320000.00),
(2, 2, 1, 1, 450000.00, 0.00, 450000.00),
(3, 3, 1, 1, 210000.00, 0.00, 210000.00);

-- ============================================================
-- CONFIGURACION GENERAL
-- ============================================================
INSERT INTO `configuracion` (`clave`, `valor`, `grupo`) VALUES
-- General
('nombre_empresa',     'SellSoft Perfumería',              'general'),
('nit_empresa',        '900123456-7',                      'general'),
('direccion_empresa',  'Calle 15 #8-32, Bogotá D.C.',      'general'),
('telefono_empresa',   '601-234-5678',                     'general'),
('correo_empresa',     'info@sellsoft.co',                  'general'),
('sitio_web',          'https://sellsoft.co',               'general'),
('zona_horaria',       'America/Bogota',                   'general'),
('idioma',             'es_CO',                            'general'),
-- Moneda
('moneda_codigo',      'COP',                              'moneda'),
('moneda_simbolo',     '$',                                'moneda'),
('moneda_nombre',      'Pesos Colombianos',                'moneda'),
('decimales',          '0',                                'moneda'),
('separador_miles',    '.',                                'moneda'),
('separador_decimal',  ',',                                'moneda'),
-- Impuestos
('iva_activo',         '1',                                'impuesto'),
('iva_porcentaje',     '19',                               'impuesto'),
('iva_nombre',         'IVA',                              'impuesto'),
('incluir_iva_precio', '1',                                'impuesto'),
-- Facturación
('prefijo_factura',    'VTA',                              'factura'),
('siguiente_numero',   '4',                                'factura'),
('formato_numero',     '000000',                           'factura'),
('pie_factura',        'Gracias por su compra.',           'factura'),
('mostrar_qr',         '1',                                'factura'),
-- Seguridad
('tiempo_sesion',      '120',                              'seguridad'),
('pin_reversion',      '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'seguridad'),
-- Correo SMTP
('smtp_host',          'smtp.gmail.com',                   'correo'),
('smtp_puerto',        '587',                              'correo'),
('smtp_usuario',       '',                                 'correo'),
('smtp_clave',         '',                                 'correo'),
('smtp_seguridad',     'tls',                              'correo'),
('correo_remitente',   'noreply@sellsoft.co',              'correo'),
('nombre_remitente',   'SellSoft Sistema',                 'correo');

SET FOREIGN_KEY_CHECKS = 1;
