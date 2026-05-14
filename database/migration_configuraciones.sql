CREATE TABLE IF NOT EXISTS configuraciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  clave VARCHAR(80) NOT NULL UNIQUE,
  valor TEXT NOT NULL,
  fecha_actualizacion TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

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
