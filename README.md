# FALEX Fábrica Textil - Versión Netlify

Este proyecto tiene una versión estática lista para publicar en Netlify. Netlify no ejecuta PHP ni MySQL, por eso la carpeta que se debe subir es `netlify/`.

## Carpeta para publicar

Sube o configura como **Publish directory**:

```text
netlify/
```

Estructura principal:

```text
netlify/
├── index.html
├── nosotros.html
├── servicios.html
├── catalogo.html
├── galeria.html
├── contacto.html
├── gracias.html
├── admin.html
├── _redirects
├── css/
├── js/
├── assets/
└── uploads/products/
```

## Publicar en Netlify

1. Entra a Netlify.
2. Crea un nuevo sitio.
3. Sube la carpeta `netlify/` o configura esa carpeta como directorio de publicación.
4. No necesitas comando de build.
5. El archivo principal es `index.html`.

## Formulario de contacto

El formulario de `contacto.html` está preparado para **Netlify Forms**:

```html
<form name="contacto" method="POST" action="gracias.html" data-netlify="true" netlify>
```

Los mensajes enviados aparecerán en el panel de Netlify, dentro de **Forms**.

## Qué funciona en Netlify

- Página de inicio.
- Nosotros.
- Servicios.
- Catálogo estático.
- Galería estática.
- Contacto con Netlify Forms.
- Botones de WhatsApp.
- Navegación entre páginas HTML.
- Archivo `_redirects` para evitar errores 404 en rutas internas.

## Qué no funciona en Netlify

Netlify no ejecuta PHP, por lo tanto no funcionará directamente:

- Login.
- Panel administrativo.
- Usuarios y roles.
- Base de datos MySQL.
- Instalador PHP.
- CRUD de productos.
- Subida de imágenes desde el panel.

Esas funciones siguen existiendo en la versión PHP del proyecto, pero requieren hosting con PHP y MySQL, como XAMPP, cPanel, Hostinger, DonWeb, etc.

## Archivos PHP

Los archivos `.php` se conservan como versión dinámica/local del sistema, pero no deben subirse como sitio final a Netlify.

Para Netlify usa únicamente la carpeta:

```text
netlify/
```

## Actualizar catálogo o galería

La versión estática usa una copia fija de los productos e imágenes existentes al momento de generar los HTML.

Si cambias productos desde el panel PHP local:

1. Actualiza productos e imágenes en el sistema local.
2. Regenera o actualiza los archivos estáticos.
3. Vuelve a subir la carpeta `netlify/` a Netlify.

## Alternativas para administración en Netlify

Para tener administración real en Netlify se necesita reescribir la parte dinámica usando una solución compatible, por ejemplo:

- Netlify Functions.
- Supabase.
- Firebase.
- Airtable.
- Headless CMS.
- Base de datos externa con API.

## Archivos importantes

- Sitio estático: `netlify/`
- Estilos: `netlify/css/styles.css`
- JavaScript: `netlify/js/main.js`
- Redirecciones: `netlify/_redirects`
- Formulario: `netlify/contacto.html`
