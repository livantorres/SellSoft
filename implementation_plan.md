# SellSoft — Sistema ERP Ligero para Retail Multi-Sede

## Descripción General

Sistema de gestión comercial completo (ERP ligero) construido en PHP 7.4+ nativo con patrón MVC, MySQL 5.7+ y Bootstrap 5. Diseñado para perfumerías con múltiples sedes pero adaptable a cualquier negocio de retail. Entrega por etapas: cada etapa es funcional y deployable.

---

## Etapas de Entrega

| Etapa | Módulos | Estado |
|-------|---------|--------|
| **Etapa 1** | DER + SQL + Estructura MVC + Auth + Dashboard | 🔜 Primero |
| **Etapa 2** | Productos (CRUD + galería + atributos + drag&drop) | Pendiente |
| **Etapa 3** | Categorías + Marcas + Proveedores + Clientes | Pendiente |
| **Etapa 4** | Punto de Venta (POS) + Inventario por sede | Pendiente |
| **Etapa 5** | Reportes + Exportación Excel/PDF | Pendiente |
| **Etapa 6** | Facturación electrónica + Envío por correo | Pendiente |
| **Etapa 7** | Landing Ecommerce + Carrito público | Pendiente |
| **Etapa 8** | Ofertas/Descuentos + Notificaciones + API REST | Pendiente |

---

## Diagrama Entidad-Relación (DER)

```
┌──────────────┐     ┌──────────────────┐     ┌───────────────┐
│    users     │────▶│   user_roles     │◀────│    roles      │
├──────────────┤     └──────────────────┘     ├───────────────┤
│ id           │                              │ id            │
│ name         │     ┌──────────────────┐     │ name          │
│ email        │     │  role_permissions│     │ slug          │
│ password     │     ├──────────────────┤     └───────────────┘
│ remember_tok │     │ role_id (FK)     │           │
│ warehouse_id │     │ permission_id(FK)│           │
│ active       │     └──────────────────┘     ┌───────────────┐
│ created_at   │                              │  permissions  │
└──────┬───────┘                              ├───────────────┤
       │                                      │ id            │
       │ ┌─────────────────────────────────── │ module        │
       │ │                                    │ action        │
       ▼ ▼                                    └───────────────┘
┌──────────────┐
│  warehouses  │◀──────────────────────────────────────────────┐
├──────────────┤                                               │
│ id           │                                               │
│ name         │     ┌─────────────────┐                      │
│ address      │     │product_warehouse│                      │
│ phone        │     ├─────────────────┤                      │
│ manager_id   │◀────│ warehouse_id(FK)│────▶┌─────────────┐  │
│ schedule     │     │ product_id (FK) │     │  products   │  │
│ active       │     │ stock           │     ├─────────────┤  │
└──────────────┘     │ stock_min       │     │ id          │  │
                     └─────────────────┘     │ name        │  │
                                             │ slug        │  │
┌──────────────┐                             │ sku         │  │
│  categories  │◀────────────────────────────│ category_id │  │
├──────────────┤                             │ subcategory │  │
│ id           │     ┌──────────────┐        │ brand_id    │  │
│ parent_id    │     │   brands     │◀───────│ provider_id │  │
│ name         │     ├──────────────┤        │ type_id     │  │
│ slug         │     │ id           │        │ price_buy   │  │
│ image        │     │ name         │        │ price_sell  │  │
│ order        │     │ logo         │        │ weight      │  │
└──────────────┘     └──────────────┘        │ dimensions  │  │
                                             │ image_main  │  │
┌──────────────┐                             │ active      │  │
│  providers   │◀────────────────────────────│ featured    │  │
├──────────────┤                             │ order_pos   │  │
│ id           │                             │ created_at  │  │
│ name         │                             └──────┬──────┘  │
│ ruc          │                                    │         │
│ email        │     ┌──────────────────┐           │         │
│ phone        │     │  product_gallery  │◀──────────┘         │
│ address      │     ├──────────────────┤                     │
└──────────────┘     │ id               │                     │
                     │ product_id (FK)  │                     │
                     │ image_url        │                     │
                     │ order            │                     │
                     └──────────────────┘                     │
                                                              │
┌──────────────┐     ┌──────────────────┐                    │
│   clients    │     │product_attributes│                    │
├──────────────┤     ├──────────────────┤                    │
│ id           │     │ id               │                    │
│ name         │     │ product_id (FK)  │                    │
│ dni/ruc      │     │ attr_name        │                    │
│ email        │     │ attr_value       │                    │
│ phone        │     └──────────────────┘                    │
│ address      │                                             │
└──────┬───────┘     ┌──────────────────┐                    │
       │             │     offers       │                    │
       │             ├──────────────────┤                    │
       │             │ id               │                    │
       │             │ name             │                    │
       │             │ type (%)         │                    │
       │             │ value            │                    │
       │             │ scope (prod/cat) │                    │
       │             │ ref_id           │                    │
       │             │ start_date       │                    │
       │             │ end_date         │                    │
       │             │ active           │                    │
       │             └──────────────────┘                    │
       │                                                     │
       ▼             ┌──────────────────┐                    │
┌──────────────┐     │     sales        │                    │
│sale_details  │◀────├──────────────────┤                    │
├──────────────┤     │ id               │                    │
│ id           │     │ code             │────────────────────┘
│ sale_id (FK) │     │ user_id (FK)     │
│ product_id   │     │ client_id (FK)   │
│ warehouse_id │     │ warehouse_id(FK) │
│ qty          │     │ subtotal         │
│ unit_price   │     │ tax              │
│ discount     │     │ discount         │
│ total        │     │ total            │
└──────────────┘     │ payment_method   │
                     │ type (factura)   │
                     │ status           │
                     │ reversed_by      │
                     │ reversed_at      │
                     │ created_at       │
                     └──────────────────┘

┌──────────────────┐     ┌──────────────┐
│   movements      │     │   settings   │
├──────────────────┤     ├──────────────┤
│ id               │     │ id           │
│ product_id (FK)  │     │ key          │
│ warehouse_id(FK) │     │ value        │
│ user_id (FK)     │     │ group        │
│ type (in/out)    │     └──────────────┘
│ qty              │
│ reason           │     ┌──────────────────┐
│ reference_id     │     │  email_history   │
│ created_at       │     ├──────────────────┤
└──────────────────┘     │ id               │
                         │ sale_id (FK)     │
┌──────────────────┐     │ recipients       │
│  transfers       │     │ subject          │
├──────────────────┤     │ status           │
│ id               │     │ attachment_path  │
│ from_warehouse   │     │ sent_at          │
│ to_warehouse     │     │ sent_by (FK)     │
│ product_id (FK)  │     └──────────────────┘
│ qty              │
│ status           │
│ authorized_by    │
│ created_at       │
└──────────────────┘
```

---

## Estructura de Carpetas MVC

```
SellSoft/
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── ProductController.php
│   │   ├── CategoryController.php
│   │   ├── SaleController.php
│   │   ├── InventoryController.php
│   │   ├── ReportController.php
│   │   ├── ClientController.php
│   │   ├── ProviderController.php
│   │   ├── WarehouseController.php
│   │   ├── OfferController.php
│   │   ├── UserController.php
│   │   ├── SettingController.php
│   │   └── PublicController.php        ← Landing ecommerce
│   │
│   ├── Models/
│   │   ├── Model.php                   ← Base model
│   │   ├── User.php
│   │   ├── Role.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Sale.php
│   │   ├── SaleDetail.php
│   │   ├── Inventory.php
│   │   ├── Warehouse.php
│   │   ├── Client.php
│   │   ├── Provider.php
│   │   ├── Offer.php
│   │   ├── Transfer.php
│   │   └── Setting.php
│   │
│   ├── Services/
│   │   ├── AuthService.php
│   │   ├── PdfService.php              ← Dompdf wrapper
│   │   ├── EmailService.php            ← PHPMailer wrapper
│   │   ├── ImportService.php           ← CSV/XLSX importer
│   │   ├── ReportService.php
│   │   └── QrService.php
│   │
│   ├── Middleware/
│   │   ├── AuthMiddleware.php
│   │   ├── RoleMiddleware.php
│   │   └── CsrfMiddleware.php
│   │
│   └── Helpers/
│       ├── Session.php
│       ├── Flash.php
│       ├── Csrf.php
│       ├── Validator.php
│       ├── Paginator.php
│       └── Format.php
│
├── config/
│   ├── config.php                      ← DB, app settings
│   ├── routes.php                      ← Definición de rutas
│   └── permissions.php                 ← Mapa de permisos
│
├── core/
│   ├── Database.php                    ← PDO Singleton
│   ├── Router.php                      ← Router ligero
│   ├── Controller.php                  ← Base controller
│   ├── Request.php                     ← Encapsula $_REQUEST
│   ├── Response.php
│   └── View.php                        ← Motor de plantillas
│
├── public/
│   ├── index.php                       ← Entry point
│   ├── .htaccess
│   ├── assets/
│   │   ├── css/
│   │   │   ├── app.css                 ← Variables + estilos globales
│   │   │   ├── pos.css                 ← Estilos POS
│   │   │   └── landing.css             ← Estilos ecommerce
│   │   ├── js/
│   │   │   ├── app.js                  ← Módulo principal
│   │   │   ├── pos.js                  ← Lógica POS
│   │   │   ├── products.js             ← CRUD + drag&drop
│   │   │   ├── reports.js              ← Chart.js
│   │   │   └── cart.js                 ← Carrito público
│   │   ├── img/
│   │   └── uploads/
│   │       ├── products/
│   │       └── logos/
│
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── main.php                ← Layout admin
│       │   ├── auth.php                ← Layout auth
│       │   └── public.php              ← Layout landing
│       ├── auth/
│       │   ├── login.php
│       │   └── register.php
│       ├── dashboard/
│       │   └── index.php
│       ├── products/
│       │   ├── index.php
│       │   ├── create.php
│       │   ├── edit.php
│       │   └── show.php
│       ├── sales/
│       │   ├── pos.php
│       │   ├── index.php
│       │   └── show.php
│       ├── reports/
│       │   └── index.php
│       ├── warehouses/
│       │   ├── index.php
│       │   └── transfers.php
│       ├── clients/
│       ├── providers/
│       ├── settings/
│       └── public/
│           ├── home.php
│           └── product.php
│
├── storage/
│   ├── pdfs/                           ← Facturas generadas
│   ├── exports/                        ← Reportes exportados
│   ├── cache/                          ← Query cache
│   └── logs/
│       └── app.log
│
├── vendor/                             ← Composer (Dompdf, PHPMailer, etc.)
├── database/
│   ├── schema.sql                      ← Estructura completa
│   └── seeders.sql                     ← Datos de prueba
│
├── composer.json
├── .htaccess                           ← Redirige a public/
├── .env.example
└── README.md
```

---

## Stack de Dependencias (Composer)

```json
{
  "require": {
    "php": ">=7.4",
    "dompdf/dompdf": "^2.0",
    "phpmailer/phpmailer": "^6.8",
    "phpoffice/phpspreadsheet": "^1.29",
    "chillerlan/php-qrcode": "^4.3"
  }
}
```

**Frontend (CDN únicamente):**
- Bootstrap 5.3
- SweetAlert2
- FontAwesome 6
- Chart.js 4
- SortableJS 1.15

---

## Decisiones de Arquitectura

### Router
Router ligero nativo (~150 líneas) con soporte para:
- Rutas con parámetros (`/products/{id}`)
- Grupos con prefijos y middleware
- Métodos HTTP (GET, POST, PUT, DELETE)
- Named routes

### Seguridad
- CSRF token en formularios (doble submit cookie + form field)
- Prepared Statements en TODAS las queries
- `password_hash(PASSWORD_BCRYPT)` para contraseñas
- Sanitización de salida con `htmlspecialchars()`
- Validación de tipo MIME en uploads de imágenes
- Rate limiting básico (sesión) en login

### Multitenancy (Multi-sede)
- `warehouse_id` en `$_SESSION` al iniciar sesión
- Selector en navbar superior con AJAX para cambio en caliente
- Todas las queries de stock incluyen `WHERE warehouse_id = ?`
- Reportes con filtro opcional por sede o consolidado

---

## Plan de Entrega — Etapa 1 (ACTUAL)

### Archivos a crear en esta etapa:

#### Base del sistema
1. `.htaccess` (raíz)
2. `public/.htaccess` + `public/index.php`
3. `config/config.php`
4. `config/routes.php`
5. `core/Database.php`
6. `core/Router.php`
7. `core/Controller.php`
8. `core/View.php`
9. `core/Request.php`

#### Helpers
10. `app/Helpers/Session.php`
11. `app/Helpers/Flash.php`
12. `app/Helpers/Csrf.php`
13. `app/Helpers/Validator.php`
14. `app/Helpers/Format.php`

#### Auth
15. `app/Models/Model.php` (base)
16. `app/Models/User.php`
17. `app/Models/Role.php`
18. `app/Services/AuthService.php`
19. `app/Middleware/AuthMiddleware.php`
20. `app/Controllers/AuthController.php`
21. `resources/views/layouts/auth.php`
22. `resources/views/layouts/main.php`
23. `resources/views/auth/login.php`

#### Dashboard
24. `app/Models/Setting.php`
25. `app/Controllers/DashboardController.php`
26. `resources/views/dashboard/index.php`

#### Assets
27. `public/assets/css/app.css`
28. `public/assets/js/app.js`

#### Base de datos
29. `database/schema.sql` (tablas completas)
30. `database/seeders.sql` (datos de prueba)

#### Composer
31. `composer.json`
32. `.env.example`
33. `README.md`

---

## Verificación de Etapa 1

- [ ] Login funcional con validación
- [ ] Redirección por roles
- [ ] Dashboard con métricas reales (ventas hoy, productos bajos, etc.)
- [ ] Selector de sede en navbar
- [ ] CSRF activo en login
- [ ] Layout responsivo con menú colapsable
- [ ] Base de datos con datos de prueba (admin/admin123)
