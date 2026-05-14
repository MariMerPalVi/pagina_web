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

UPDATE solicitudes_cotizacion SET estado = 'no_respondida' WHERE estado = 'cerrada';

ALTER TABLE solicitudes_cotizacion
  MODIFY estado ENUM('pendiente', 'respondida', 'no_respondida') NOT NULL DEFAULT 'pendiente';

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

CREATE TABLE IF NOT EXISTS ordenes_trabajo_imagenes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  orden_id INT NOT NULL,
  imagen VARCHAR(255) NOT NULL,
  fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_ordenes_imagenes_orden FOREIGN KEY (orden_id) REFERENCES ordenes_trabajo(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ordenes_trabajo_responsables (
  orden_id INT NOT NULL,
  usuario_id INT NOT NULL,
  fecha_asignacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (orden_id, usuario_id),
  CONSTRAINT fk_ordenes_responsables_orden FOREIGN KEY (orden_id) REFERENCES ordenes_trabajo(id) ON DELETE CASCADE,
  CONSTRAINT fk_ordenes_responsables_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT IGNORE INTO ordenes_trabajo_responsables (orden_id, usuario_id)
SELECT id, asignado_a
FROM ordenes_trabajo
WHERE asignado_a IS NOT NULL;

INSERT INTO configuraciones (clave, valor)
SELECT 'social_instagram', ''
WHERE NOT EXISTS (SELECT 1 FROM configuraciones WHERE clave = 'social_instagram');

INSERT INTO configuraciones (clave, valor)
SELECT 'social_facebook', ''
WHERE NOT EXISTS (SELECT 1 FROM configuraciones WHERE clave = 'social_facebook');

INSERT INTO configuraciones (clave, valor)
SELECT 'social_tiktok', ''
WHERE NOT EXISTS (SELECT 1 FROM configuraciones WHERE clave = 'social_tiktok');

INSERT INTO configuraciones (clave, valor)
SELECT 'footer_link_1_label', 'Inicio'
WHERE NOT EXISTS (SELECT 1 FROM configuraciones WHERE clave = 'footer_link_1_label');

INSERT INTO configuraciones (clave, valor)
SELECT 'footer_link_1_url', 'index.php'
WHERE NOT EXISTS (SELECT 1 FROM configuraciones WHERE clave = 'footer_link_1_url');

INSERT INTO configuraciones (clave, valor)
SELECT 'footer_link_2_label', 'Catálogo'
WHERE NOT EXISTS (SELECT 1 FROM configuraciones WHERE clave = 'footer_link_2_label');

INSERT INTO configuraciones (clave, valor)
SELECT 'footer_link_2_url', 'catalogo.php'
WHERE NOT EXISTS (SELECT 1 FROM configuraciones WHERE clave = 'footer_link_2_url');

INSERT INTO configuraciones (clave, valor)
SELECT 'footer_link_3_label', 'Contacto'
WHERE NOT EXISTS (SELECT 1 FROM configuraciones WHERE clave = 'footer_link_3_label');

INSERT INTO configuraciones (clave, valor)
SELECT 'footer_link_3_url', 'contacto.php'
WHERE NOT EXISTS (SELECT 1 FROM configuraciones WHERE clave = 'footer_link_3_url');
