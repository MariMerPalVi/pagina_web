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
  estado ENUM('pendiente', 'respondida', 'cerrada') NOT NULL DEFAULT 'pendiente',
  respondido_por INT NULL,
  fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_respuesta TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT fk_solicitudes_usuario FOREIGN KEY (respondido_por) REFERENCES usuarios(id) ON DELETE SET NULL
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
