-- ==========================================================
-- Mokoka Butchery - Database Setup
-- Import this file in phpMyAdmin (Import tab) or run it in the SQL tab.
-- ==========================================================

CREATE DATABASE IF NOT EXISTS butchery
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE butchery;

-- Products and their price per kg.
-- The server uses these prices, not the browser, when saving orders.
CREATE TABLE IF NOT EXISTS products (
    id            VARCHAR(50)   NOT NULL PRIMARY KEY,
    name          VARCHAR(100)  NOT NULL,
    price_per_kg  DECIMAL(10,2) NOT NULL,
    is_active     TINYINT(1)    NOT NULL DEFAULT 1
) ENGINE=InnoDB;

-- Order requests submitted from the Products page
CREATE TABLE IF NOT EXISTS orders (
    id              INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    order_ref       VARCHAR(20)   NOT NULL UNIQUE,
    customer_name   VARCHAR(100)  NOT NULL,
    phone           VARCHAR(30)   NOT NULL,
    email           VARCHAR(150)  NULL,
    collection_type ENUM('collection', 'delivery') NOT NULL DEFAULT 'collection',
    notes           TEXT          NULL,
    total           DECIMAL(10,2) NOT NULL,
    status          ENUM('new', 'confirmed', 'ready', 'completed', 'cancelled') NOT NULL DEFAULT 'new',
    created_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Individual items within each order
CREATE TABLE IF NOT EXISTS order_items (
    id            INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    order_id      INT UNSIGNED  NOT NULL,
    product_id    VARCHAR(50)   NOT NULL,
    product_name  VARCHAR(100)  NOT NULL,
    grams         INT UNSIGNED  NOT NULL,
    qty           INT UNSIGNED  NOT NULL,
    price_per_kg  DECIMAL(10,2) NOT NULL,
    line_total    DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Messages sent from the Contact Us page
CREATE TABLE IF NOT EXISTS messages (
    id          INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL,
    phone       VARCHAR(30)   NULL,
    subject     VARCHAR(50)   NOT NULL,
    message     TEXT          NOT NULL,
    is_read     TINYINT(1)    NOT NULL DEFAULT 0,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Starter product list. The prices are examples: change them to your real prices.
-- Keep these in line with the data-price-per-kg values in products.html.
INSERT INTO products (id, name, price_per_kg, is_active) VALUES
    ('boerewors',   'Homemade Boerewors', 110.00, 1),
    ('ribeye',      'Beef Ribeye',        250.00, 1),
    ('beef-mince',  'Beef Mince',          95.00, 1),
    ('pork-chops',  'Pork Chops',         105.00, 1),
    ('lamb-chops',  'Lamb Chops',         170.00, 1),
    ('chicken',     'Whole Chicken',       70.00, 1)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Admin users table (used by admin/login.php)
CREATE TABLE IF NOT EXISTS admins (
    id            INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)   NOT NULL UNIQUE,
    password_hash VARCHAR(255)  NOT NULL
) ENGINE=InnoDB;

-- Demo admin account.
-- Username: admin  |  Password: admin123  (DEMO ONLY — change before going live)
-- Hash generated with: password_hash('admin123', PASSWORD_DEFAULT) on this machine
DELETE FROM admins WHERE username = 'admin';
INSERT INTO admins (username, password_hash) VALUES
    ('admin', '$2y$10$m8Uw.kzJPEVdJ1YUlohFj.OxkXwcv0G3at1yBjC7Q8z4AWwigzCKC');

-- ==========================================================
-- Demo data for admin panel screenshots
-- ==========================================================

INSERT INTO orders (order_ref, customer_name, phone, email, collection_type, notes, total, status, created_at) VALUES
    ('ORD-2026-0001', 'Thabo Mokoena',  '0712345678', 'thabo@example.com',   'collection', 'Please cut the ribeye thick.',   320.50, 'new',       '2026-10-01 09:15:00'),
    ('ORD-2026-0002', 'Nomsa Dlamini',  '0834567890', 'nomsa@example.com',   'delivery',   'Deliver before 12pm please.',    187.00, 'confirmed', '2026-10-02 11:30:00'),
    ('ORD-2026-0003', 'Sipho Nkosi',    '0761239876', '',                    'collection', '',                               94.50,  'completed', '2026-10-03 08:45:00');

INSERT INTO order_items (order_id, product_id, product_name, grams, qty, price_per_kg, line_total) VALUES
    (1, 'ribeye',     'Beef Ribeye',        1000, 1, 250.00, 250.00),
    (1, 'boerewors',  'Homemade Boerewors',  500, 1, 110.00,  55.00),
    (1, 'beef-mince', 'Beef Mince',          500, 1,  95.00,  47.50),
    (2, 'pork-chops', 'Pork Chops',         1000, 1, 105.00, 105.00),
    (2, 'chicken',    'Whole Chicken',      1000, 1,  70.00,  70.00),
    (2, 'beef-mince', 'Beef Mince',          500, 1,  95.00,  47.50),
    (3, 'boerewors',  'Homemade Boerewors', 1000, 1, 110.00, 110.00);

INSERT INTO messages (name, email, phone, subject, message, is_read, created_at) VALUES
    ('Lerato Khumalo', 'lerato@example.com', '0791234567', 'general',  'Do you deliver to Sebokeng?',                           0, '2026-10-01 10:00:00'),
    ('John Smith',     'john@example.com',   '0823456789', 'order',    'I placed an order but have not received confirmation.',  0, '2026-10-02 14:20:00'),
    ('Zanele Motha',   'zanele@example.com', '',           'general',  'What time do you open on Saturdays?',                   1, '2026-10-03 09:05:00');
