-- =====================================================================
-- MLA Tech - esquema de base de datos (MySQL 8.0.13+ / MariaDB 10.2.1+)
-- Uso: mysql -u root -p < schema.sql
-- =====================================================================

CREATE DATABASE IF NOT EXISTS mlatech CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mlatech;

SET NAMES utf8mb4;

-- Usuarios. La contrasena guarda el hash de password_hash() (bcrypt = 60 chars; 255 deja lugar a algoritmos futuros).
CREATE TABLE IF NOT EXISTS users (
  id         CHAR(36)     NOT NULL DEFAULT (UUID()),
  name       VARCHAR(50)  NOT NULL,
  email      VARCHAR(150) NOT NULL,
  password   VARCHAR(255) NOT NULL,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB;

-- Categorias de productos (los iconos se asocian por nombre en src/config/app.php).
CREATE TABLE IF NOT EXISTS categories (
  id         CHAR(36)    NOT NULL DEFAULT (UUID()),
  name       VARCHAR(50) NOT NULL,
  created_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_categories_name (name)
) ENGINE=InnoDB;

-- Productos. ON DELETE RESTRICT: no se puede borrar una categoria que todavia tiene productos.
CREATE TABLE IF NOT EXISTS products (
  id          CHAR(36)     NOT NULL DEFAULT (UUID()),
  category_id CHAR(36)     NOT NULL,
  name        VARCHAR(150) NOT NULL,
  description TEXT         NULL,
  price       DECIMAL(10,2) NOT NULL,
  stock       INT UNSIGNED NOT NULL DEFAULT 0,
  image_url   VARCHAR(255) NULL,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_products_category (category_id),
  KEY idx_products_created_at (created_at),   -- acelera "los ultimos productos" del home
  CONSTRAINT fk_products_category
    FOREIGN KEY (category_id) REFERENCES categories(id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Mensajes del formulario de contacto / soporte
CREATE TABLE IF NOT EXISTS contact_messages (
  id         CHAR(36)     NOT NULL DEFAULT (UUID()),
  name       VARCHAR(100) NOT NULL,
  email      VARCHAR(150) NOT NULL,
  subject    VARCHAR(150) NOT NULL,
  message    TEXT         NOT NULL,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Datos de ejemplo
-- ---------------------------------------------------------------------
INSERT INTO categories (name) VALUES
  ('Procesadores'),
  ('Placas de video'),
  ('Memorias RAM'),
  ('Almacenamiento'),
  ('Perifericos'),
  ('Accesorios'),
  ('Motherboards'),
  ('Gabinetes'),
  ('Fuentes de poder');


INSERT INTO products (category_id, name, description, price, stock, image_url) VALUES
  ((SELECT id FROM categories WHERE name = 'Procesadores'),
   'Procesador Ryzen 5 5600', '6 nucleos / 12 hilos, socket AM4.', 120000.00, 15, '/assets/img/products/ryzen-5-5600.jpg'),
  ((SELECT id FROM categories WHERE name = 'Procesadores'),
   'Procesador Intel Core i9-13900K', '24 nucleos, socket LGA1700, ideal para gaming y produccion.', 650000.00, 6, '/assets/img/products/intel-core-i9-13900k.jpg'),
  ((SELECT id FROM categories WHERE name = 'Procesadores'),
   'Procesador Ryzen 7 7700X', '8 nucleos / 16 hilos, socket AM5.', 320000.00, 10, '/assets/img/products/ryzen-7-7700x.jpg'),
  ((SELECT id FROM categories WHERE name = 'Placas de video'),
   'Placa de video RTX 4060', '8GB GDDR6, ideal para gaming en 1080p/1440p.', 480000.00, 8, '/assets/img/products/rtx-4060.jpg'),
  ((SELECT id FROM categories WHERE name = 'Placas de video'),
   'Placa de video RTX 4090', '24GB GDDR6X, tope de gama.', 1850000.00, 3, '/assets/img/products/rtx-4090.jpg'),
  ((SELECT id FROM categories WHERE name = 'Placas de video'),
   'Placa de video RX 7600', '8GB GDDR6, buena relacion precio/rendimiento.', 350000.00, 12, '/assets/img/products/rx-7600.jpg'),
  ((SELECT id FROM categories WHERE name = 'Memorias RAM'),
   'Memoria RAM 16GB DDR4', 'Kit de 2x8GB, 3200MHz.', 45000.00, 30, '/assets/img/products/ram-16gb-ddr4.jpg'),
  ((SELECT id FROM categories WHERE name = 'Memorias RAM'),
   'Memoria RAM 32GB DDR5', 'Kit de 2x16GB, 6000MHz, con disipador RGB.', 130000.00, 18, '/assets/img/products/ram-32gb-ddr5.jpg'),
  ((SELECT id FROM categories WHERE name = 'Almacenamiento'),
   'SSD NVMe 1TB', 'Lectura hasta 3500MB/s.', 85000.00, 25, '/assets/img/products/ssd-nvme-1tb.jpg'),
  ((SELECT id FROM categories WHERE name = 'Almacenamiento'),
   'Disco rigido HDD 2TB', '7200RPM, ideal para almacenamiento masivo.', 60000.00, 20, '/assets/img/products/hdd-2tb.jpg'),
  ((SELECT id FROM categories WHERE name = 'Perifericos'),
   'Teclado mecanico RGB', 'Switches rojos, retroiluminacion RGB.', 38000.00, 20, '/assets/img/products/teclado-mecanico-rgb.jpg'),
  ((SELECT id FROM categories WHERE name = 'Perifericos'),
   'Mouse gamer 16000 DPI', 'Sensor optico de alta precision, 6 botones.', 22000.00, 35, '/assets/img/products/mouse-gamer-16000-dpi.jpg'),
  ((SELECT id FROM categories WHERE name = 'Perifericos'),
   'Monitor 24" 144Hz', 'Full HD, panel IPS, ideal para gaming.', 210000.00, 9, '/assets/img/products/monitor-24-144hz.jpg'),
  ((SELECT id FROM categories WHERE name = 'Accesorios'),
   'Auriculares gamer 7.1', 'Sonido envolvente, microfono desmontable.', 55000.00, 22, '/assets/img/products/auriculares-gamer-71.jpg'),
  ((SELECT id FROM categories WHERE name = 'Accesorios'),
   'Mousepad XL', '900x400mm, base antideslizante.', 12000.00, 40, '/assets/img/products/mousepad-xl.jpg'),
  ((SELECT id FROM categories WHERE name = 'Fuentes de poder'),
   'Fuente de alimentacion 650W 80+ Bronze', 'Certificada, modular semi.', 75000.00, 14, '/assets/img/products/fuente-650w-bronze.jpg'),
  ((SELECT id FROM categories WHERE name = 'Motherboards'),
   'Motherboard B550M AM4', 'Socket AM4, micro-ATX, memoria DDR4. Compatible con Ryzen 5 5600.', 115000.00, 12, NULL),
  ((SELECT id FROM categories WHERE name = 'Motherboards'),
   'Motherboard B650M AM5', 'Socket AM5, micro-ATX, memoria DDR5. Compatible con Ryzen 7 7700X.', 190000.00, 9, NULL),
  ((SELECT id FROM categories WHERE name = 'Motherboards'),
   'Motherboard Z790 LGA1700', 'Socket LGA1700, ATX, memoria DDR5. Compatible con Intel Core i9-13900K.', 380000.00, 5, NULL),
  ((SELECT id FROM categories WHERE name = 'Gabinetes'),
   'Gabinete gamer ATX vidrio templado', 'Mid tower ATX, panel lateral de vidrio templado y 3 coolers RGB.', 68000.00, 14, NULL),
  ((SELECT id FROM categories WHERE name = 'Gabinetes'),
   'Gabinete Mini Tower mATX', 'Formato compacto micro-ATX, ideal para armados chicos.', 38000.00, 18, NULL),
  ((SELECT id FROM categories WHERE name = 'Fuentes de poder'),
   'Fuente de alimentacion 750W 80+ Gold', 'Certificada, full modular.', 105000.00, 10, NULL),
  ((SELECT id FROM categories WHERE name = 'Fuentes de poder'),
   'Fuente de alimentacion 550W 80+ White', 'Para equipos de gama media y baja.', 52000.00, 16, NULL);