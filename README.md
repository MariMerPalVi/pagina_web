# FALEX Fábrica Textil - PHP para InfinityFree

Proyecto web en PHP/MySQL para una fábrica textil. Esta versión está preparada para publicarse en un hosting tradicional compatible con PHP, como InfinityFree.

## Requisitos

- Hosting con PHP.
- Base de datos MySQL.
- Acceso al administrador de archivos o FTP.
- En InfinityFree, subir el proyecto dentro de la carpeta `/htdocs/`.

## Archivos principales

- Página pública: `index.php`
- Nosotros: `nosotros.php`
- Servicios: `servicios.php`
- Catálogo dinámico: `catalogo.php`
- Galería dinámica: `galeria.php`
- Contacto: `contacto.php`
- Login: `login.php`
- Panel administrativo: `admin/`
- Conexión MySQL: `config/database.php`
- Base de datos: `database/schema.sql`

## Configurar base de datos en InfinityFree

En el panel de InfinityFree crea una base de datos MySQL. Luego edita:

```text
config/database.php
```

Debes reemplazar manualmente estos valores con los datos reales que te entrega InfinityFree:

```php
const DB_HOST = '...';
const DB_PORT = '3306';
const DB_NAME = '...';
const DB_USER = '...';
const DB_PASS = '...';
```

No uses los datos de XAMPP en producción.

## Importar la base de datos

1. Entra a phpMyAdmin desde InfinityFree.
2. Selecciona la base de datos creada.
3. Importa el archivo recomendado para InfinityFree:

```text
database/infinityfree.sql
```

Este archivo no intenta crear la base de datos, porque InfinityFree requiere que la base ya exista. También crea un usuario inicial:

- Usuario: `admin`
- Contraseña: `admin123`

Cambia esa contraseña después de entrar al panel.

El archivo `database/schema.sql` queda para entornos locales donde sí puedes crear la base de datos desde SQL.

## Publicar en InfinityFree

1. Sube el contenido del proyecto a `/htdocs/`.
2. Asegúrate de que `index.php` quede directamente dentro de `/htdocs/`.
3. Sube también:
   - `admin/`
   - `assets/`
   - `css/`
   - `js/`
   - `includes/`
   - `config/`
   - `uploads/`
4. Configura `config/database.php` con los datos reales de InfinityFree.
5. Importa `database/infinityfree.sql`.

## Panel administrativo

El panel está en:

```text
/login.php
```

Desde ahí puedes administrar:

- Productos.
- Categorías.
- Empleados.
- Imágenes del catálogo.
- Galería pública basada en productos activos con imagen.

## Formularios

El formulario de contacto usa comportamiento PHP/HTML tradicional. No usa Netlify Forms.

Si deseas guardar mensajes en base de datos o enviarlos por correo con PHP, se puede agregar un procesador como `procesar_contacto.php`.

## Notas importantes

- Netlify no se usa en esta versión.
- No se requiere `_redirects`.
- No se usan archivos `.html` duplicados.
- El sitio principal vuelve a ser `index.php`.
- La carpeta `uploads/products/` debe tener permisos de escritura para poder subir imágenes desde el panel.

## Desarrollo local con XAMPP

Para trabajar localmente:

1. Coloca el proyecto en:

```text
C:\xampp\htdocs\pagina_web
```

2. Inicia Apache y MySQL.
3. Abre:

```text
http://localhost/pagina_web/
```

4. Configura `config/database.php` para XAMPP si es necesario.
