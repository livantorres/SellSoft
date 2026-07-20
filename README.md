# SellSoft ERP — Sistema de Gestión Comercial

> Sistema ERP ligero para retail multi-sede. Desarrollado para Colombia 🇨🇴  
> Moneda: COP · IVA: 19% · Zona horaria: America/Bogota

---

## 🚀 Instalación Rápida en Laragon

### 1. Clonar / descomprimir en Laragon

El proyecto ya está en `C:\laragon\www\SellSoft`.

### 2. Configurar el Virtual Host en Laragon

1. Abrir **Laragon** → Click derecho en ícono de bandeja → **Virtual Hosts** → **Add**
2. Configurar:
   - **Host:** `sellsoft.test`
   - **Document Root:** `C:\laragon\www\SellSoft\publico`
3. Hacer click en **Add** → Laragon actualiza automáticamente el `hosts` de Windows.
4. Verificar en el navegador: `http://sellsoft.test`

### 3. Crear la base de datos

1. Abrir **HeidiSQL** (incluido en Laragon) o **phpMyAdmin**
2. Ejecutar primero: `basedatos/esquema.sql`
3. Luego: `basedatos/semillas.sql`

```bash
# Alternativa desde la terminal de Laragon:
mysql -u root -e "source C:/laragon/www/SellSoft/basedatos/esquema.sql"
mysql -u root -e "source C:/laragon/www/SellSoft/basedatos/semillas.sql"
```

### 4. Configurar variables de entorno

```bash
# En la raíz del proyecto:
copy .env.example .env
```

Editar `.env` con los datos de tu Laragon (generalmente `DB_USUARIO=root`, `DB_CLAVE=` vacía).

### 5. Instalar dependencias PHP (Composer)

```bash
# Desde la terminal de Laragon:
cd C:\laragon\www\SellSoft
composer install
```

### 6. Crear directorios de almacenamiento

```bash
mkdir almacenamiento\pdfs almacenamiento\exportaciones almacenamiento\cache almacenamiento\registros
mkdir publico\activos\subidas\productos publico\activos\subidas\logos
```

### 7. Permisos de escritura (en servidor Linux/VPS)

```bash
chmod -R 755 almacenamiento/
chmod -R 755 publico/activos/subidas/
```

---

## 👤 Credenciales de Prueba

| Rol           | Correo                  | Contraseña  |
|---------------|-------------------------|-------------|
| Administrador | admin@sellsoft.co       | `Admin2024!` |
| Vendedor      | vendedor@sellsoft.co    | `Admin2024!` |
| Almacenero    | almacen@sellsoft.co     | `Admin2024!` |
| Invitado      | invitado@sellsoft.co    | `Admin2024!` |

---

## 📁 Estructura del Proyecto

```
SellSoft/
├── app/
│   ├── Controladores/      # Lógica de cada módulo
│   ├── Modelos/            # Acceso a datos (PDO)
│   ├── Servicios/          # Lógica de negocio (PDF, correo, etc.)
│   ├── Middleware/         # Protección de rutas
│   └── Ayudantes/          # Utilidades (Sesion, Flash, Csrf, Formato)
├── basedatos/
│   ├── esquema.sql         ← Estructura de tablas
│   └── semillas.sql        ← Datos de prueba
├── configuracion/
│   ├── config.php          ← Configuración central
│   └── rutas.php           ← Definición de rutas
├── nucleo/                 # Framework ligero propio
│   ├── BaseDatos.php       ← Singleton PDO
│   ├── Enrutador.php       ← Router nativo
│   ├── Controlador.php     ← Clase base
│   └── Vista.php           ← Motor de plantillas
├── publico/                ← DocumentRoot (web root)
│   ├── index.php           ← Punto de entrada único
│   ├── .htaccess
│   └── activos/            ← CSS, JS, imágenes
└── recursos/
    └── vistas/             ← Plantillas HTML/PHP
```

---

## 🗄️ Tablas de Base de Datos

| Tabla               | Descripción                              |
|---------------------|------------------------------------------|
| `usuarios`          | Cuentas del sistema                      |
| `roles`             | Administrador, Vendedor, Almacenero...   |
| `permisos`          | Control granular por módulo y acción     |
| `bodegas`           | Sedes / almacenes del negocio            |
| `categorias`        | Árbol de categorías y subcategorías      |
| `marcas`            | Marcas de productos                      |
| `proveedores`       | Proveedores de mercancía                 |
| `productos`         | Catálogo principal                       |
| `producto_bodega`   | Stock por sede (multi-sede)              |
| `galeria_productos` | Imágenes adicionales                     |
| `atributos_productos`| Atributos personalizables               |
| `clientes`          | Base de datos de clientes                |
| `ventas`            | Registro maestro de ventas               |
| `detalle_ventas`    | Líneas de cada venta                     |
| `movimientos`       | Historial de inventario                  |
| `transferencias`    | Traslados entre sedes                    |
| `ofertas`           | Promociones y descuentos                 |
| `historial_correos` | Facturas enviadas por email              |
| `configuracion`     | Ajustes del sistema                      |

---

## 🔧 Dependencias

| Librería               | Uso                          |
|------------------------|------------------------------|
| `dompdf/dompdf`        | Generación de facturas PDF   |
| `phpmailer/phpmailer`  | Envío de correos SMTP        |
| `phpoffice/phpspreadsheet` | Importar/exportar Excel  |
| `chillerlan/php-qrcode`| Código QR en facturas        |

**Frontend (CDN):** Bootstrap 5.3 · SweetAlert2 · FontAwesome 6 · Chart.js 4

---

## 🛡️ Seguridad

- ✅ Prepared Statements en **todas** las consultas
- ✅ CSRF tokens en formularios (doble verificación)
- ✅ Contraseñas con `password_hash(PASSWORD_BCRYPT)`
- ✅ Validación de MIME en uploads de imágenes
- ✅ Headers de seguridad HTTP (X-Frame-Options, CSP)
- ✅ Rate limiting básico en login
- ✅ Sesiones con `HttpOnly`, `SameSite=Lax`, `session.use_strict_mode`

---

## 🌎 Configuración Regional Colombia

| Parámetro      | Valor                |
|----------------|----------------------|
| Zona horaria   | `America/Bogota`     |
| Moneda         | COP (Pesos)          |
| Símbolo        | `$`                  |
| IVA            | 19% (configurable)   |
| Formato moneda | `$ 1.250.000`        |
| Locale PHP     | `es_CO`              |

---

## 📋 Etapas de Desarrollo

- [x] **Etapa 1:** Infraestructura + Auth + Dashboard (COMPLETADA)
- [ ] **Etapa 2:** CRUD Productos + Galería + Drag & Drop
- [ ] **Etapa 3:** Categorías + Marcas + Proveedores + Clientes
- [ ] **Etapa 4:** Punto de Venta (POS) + Inventario Multi-Sede
- [ ] **Etapa 5:** Reportes + Exportación Excel/PDF
- [ ] **Etapa 6:** Facturación Electrónica + Envío por Correo
- [ ] **Etapa 7:** Landing Ecommerce + Carrito Público
- [ ] **Etapa 8:** Ofertas + API REST + Notificaciones

---

## 🚢 Despliegue en VPS/Servidor Compartido

1. Subir todos los archivos (excepto `vendor/`) via FTP/SFTP
2. Configurar `DocumentRoot` apuntando a `publico/`
3. En cPanel: crear BD MySQL, usuario, asignar privilegios
4. Copiar `.env.example` → `.env` y ajustar variables
5. Ejecutar `composer install --no-dev --optimize-autoloader`
6. Ejecutar scripts SQL desde phpMyAdmin
7. Ajustar permisos: `chmod 755 almacenamiento/`

---

*SellSoft ERP — Hecho con ❤️ en Colombia*
