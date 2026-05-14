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

INSERT INTO configuraciones (clave, valor)
SELECT 'social_instagram', ''
WHERE NOT EXISTS (SELECT 1 FROM configuraciones WHERE clave = 'social_instagram');

INSERT INTO configuraciones (clave, valor)
SELECT 'social_facebook', ''
WHERE NOT EXISTS (SELECT 1 FROM configuraciones WHERE clave = 'social_facebook');

INSERT INTO configuraciones (clave, valor)
SELECT 'social_tiktok', ''
WHERE NOT EXISTS (SELECT 1 FROM configuraciones WHERE clave = 'social_tiktok');
