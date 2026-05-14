CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  correo VARCHAR(160) NOT NULL,
  usuario VARCHAR(80) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  rol ENUM('administrador', 'empleado') NOT NULL DEFAULT 'empleado',
  estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
  fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  descripcion TEXT NULL,
  estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
  fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(160) NOT NULL,
  descripcion TEXT NOT NULL,
  precio DECIMAL(10,2) NOT NULL DEFAULT 0,
  categoria_id INT NOT NULL,
  imagen VARCHAR(255) NULL,
  estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
  creado_por INT NULL,
  fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_actualizacion TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_productos_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id),
  CONSTRAINT fk_productos_usuario FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS configuraciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  clave VARCHAR(80) NOT NULL UNIQUE,
  valor TEXT NOT NULL,
  fecha_actualizacion TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS solicitudes_cotizacion (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(160) NOT NULL,
  telefono VARCHAR(60) NOT NULL,
  correo VARCHAR(160) NULL,
  producto VARCHAR(160) NOT NULL,
  mensaje TEXT NOT NULL,
  respuesta TEXT NULL,
  estado ENUM('pendiente', 'respondida', 'no_respondida') NOT NULL DEFAULT 'pendiente',
  respondido_por INT NULL,
  fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_respuesta TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT fk_solicitudes_usuario FOREIGN KEY (respondido_por) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ordenes_trabajo (
  id INT AUTO_INCREMENT PRIMARY KEY,
  solicitud_id INT NULL,
  cliente_nombre VARCHAR(160) NOT NULL,
  cliente_telefono VARCHAR(60) NOT NULL,
  cliente_correo VARCHAR(160) NULL,
  producto VARCHAR(160) NOT NULL,
  cantidad VARCHAR(80) NULL,
  tallas VARCHAR(255) NULL,
  detalles TEXT NOT NULL,
  fecha_entrega DATE NULL,
  asignado_a INT NULL,
  estado ENUM('pendiente', 'en_proceso', 'finalizada') NOT NULL DEFAULT 'pendiente',
  creado_por INT NULL,
  fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_actualizacion TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_ordenes_solicitud FOREIGN KEY (solicitud_id) REFERENCES solicitudes_cotizacion(id) ON DELETE SET NULL,
  CONSTRAINT fk_ordenes_asignado FOREIGN KEY (asignado_a) REFERENCES usuarios(id) ON DELETE SET NULL,
  CONSTRAINT fk_ordenes_creador FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO categorias (nombre, descripcion, estado)
SELECT 'Cuellos escolares', 'Cuellos para uniformes escolares personalizados.', 'activo'
WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nombre = 'Cuellos escolares');

INSERT INTO categorias (nombre, descripcion, estado)
SELECT 'Cuellos empresariales', 'Cuellos para prendas corporativas y uniformes empresariales.', 'activo'
WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nombre = 'Cuellos empresariales');

INSERT INTO categorias (nombre, descripcion, estado)
SELECT 'Camisetas personalizadas', 'Camisetas para eventos, campañas, grupos y marcas.', 'activo'
WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nombre = 'Camisetas personalizadas');

INSERT INTO categorias (nombre, descripcion, estado)
SELECT 'Equipos deportivos', 'Uniformes sublimados y estampados para equipos.', 'activo'
WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nombre = 'Equipos deportivos');

INSERT INTO categorias (nombre, descripcion, estado)
SELECT 'Prendas para hombre', 'Prendas en telas de punto para hombres.', 'activo'
WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nombre = 'Prendas para hombre');

INSERT INTO categorias (nombre, descripcion, estado)
SELECT 'Prendas para mujer', 'Prendas en telas de punto para mujeres.', 'activo'
WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nombre = 'Prendas para mujer');

INSERT INTO categorias (nombre, descripcion, estado)
SELECT 'Prendas para niños', 'Prendas cómodas y personalizadas para niños.', 'activo'
WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nombre = 'Prendas para niños');

INSERT INTO categorias (nombre, descripcion, estado)
SELECT 'Bordados institucionales', 'Sellos, nombres y logotipos bordados.', 'activo'
WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nombre = 'Bordados institucionales');

INSERT INTO categorias (nombre, descripcion, estado)
SELECT 'Sublimados', 'Diseños sublimados con acabado profesional.', 'activo'
WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nombre = 'Sublimados');

INSERT INTO categorias (nombre, descripcion, estado)
SELECT 'Estampados', 'Estampados textiles para prendas personalizadas.', 'activo'
WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nombre = 'Estampados');

INSERT INTO usuarios (nombre, correo, usuario, password_hash, rol, estado)
SELECT 'Administrador FALEX', 'ventas@falextextil.com', 'admin', '$2y$10$wiG68jVpG6W94ollFok9tuvricaB88.OmdPIq2zZLCBHli2DlKyhi', 'administrador', 'activo'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE usuario = 'admin');

INSERT INTO configuraciones (clave, valor)
SELECT 'whatsapp_number', '593999999999'
WHERE NOT EXISTS (SELECT 1 FROM configuraciones WHERE clave = 'whatsapp_number');

INSERT INTO configuraciones (clave, valor)
SELECT 'contact_phone', '+593 99 999 9999'
WHERE NOT EXISTS (SELECT 1 FROM configuraciones WHERE clave = 'contact_phone');

INSERT INTO configuraciones (clave, valor)
SELECT 'contact_email', 'ventas@falextextil.com'
WHERE NOT EXISTS (SELECT 1 FROM configuraciones WHERE clave = 'contact_email');

INSERT INTO configuraciones (clave, valor)
SELECT 'contact_address', 'Quito, Ecuador'
WHERE NOT EXISTS (SELECT 1 FROM configuraciones WHERE clave = 'contact_address');

INSERT INTO configuraciones (clave, valor)
SELECT 'social_instagram', ''
WHERE NOT EXISTS (SELECT 1 FROM configuraciones WHERE clave = 'social_instagram');

INSERT INTO configuraciones (clave, valor)
SELECT 'social_facebook', ''
WHERE NOT EXISTS (SELECT 1 FROM configuraciones WHERE clave = 'social_facebook');

INSERT INTO configuraciones (clave, valor)
SELECT 'social_tiktok', ''
WHERE NOT EXISTS (SELECT 1 FROM configuraciones WHERE clave = 'social_tiktok');
