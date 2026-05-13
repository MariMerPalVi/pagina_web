# FALEX Fábrica Textil

Sistema web PHP/MySQL para una fábrica textil con página pública, catálogo dinámico, login por roles y panel administrativo.

## Requisitos

- XAMPP con Apache y MySQL activos.
- PHP incluido en XAMPP.
- Navegador web.

## Instalación

1. Copia o conserva este proyecto en `C:\xampp\htdocs\pagina_web`.
2. Inicia Apache y MySQL desde el panel de XAMPP.
3. Abre `http://localhost/pagina_web/install.php`.
4. Presiona **Crear base de datos e instalar**.
5. Entra al panel en `http://localhost/pagina_web/login.php`.

## Usuarios iniciales

- Administrador: `admin` / `admin123`
- Empleado: `empleado` / `empleado123`

Cambia estas contraseñas después de instalar el sistema en un entorno real.

## URLs principales

- Página pública: `http://localhost/pagina_web/`
- Instalador: `http://localhost/pagina_web/install.php`
- Login interno: `http://localhost/pagina_web/login.php`
- Panel: `http://localhost/pagina_web/admin/`

## Módulos

- Catálogo público con productos activos.
- Filtro público por categorías.
- Productos: crear, editar, eliminar, activar, desactivar y subir imagen.
- Categorías: crear, editar, activar y desactivar.
- Empleados: crear, editar, cambiar contraseña y activar/desactivar, solo para administrador.
- Dashboard con estadísticas.

## Configuración rápida

Los datos de contacto están en `includes/helpers.php`:

- `WHATSAPP_NUMBER`
- `CONTACT_EMAIL`
- `CONTACT_PHONE`
- `CONTACT_ADDRESS`

La conexión a base de datos está en `config/database.php`.
