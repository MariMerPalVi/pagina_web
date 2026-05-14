CREATE DATABASE IF NOT EXISTS falex_textil CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE falex_textil;

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

CREATE TABLE IF NOT EXISTS ordenes_trabajo_imagenes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  orden_id INT NOT NULL,
  imagen VARCHAR(255) NOT NULL,
  fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_ordenes_imagenes_orden FOREIGN KEY (orden_id) REFERENCES ordenes_trabajo(id) ON DELETE CASCADE
) ENGINE=InnoDB;
