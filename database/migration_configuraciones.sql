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
