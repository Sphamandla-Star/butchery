-- ==========================================================
-- Your Butchery - Database Setup
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
    ('boerewors',   'Homemade Boerewors', 120.00, 1),
    ('ribeye',      'Beef Ribeye',        250.00, 1),
    ('beef-mince',  'Beef Mince',         139.00, 1),
    ('pork-chops',  'Pork Chops',         110.00, 1),
    ('lamb-chops',  'Lamb Chops',         240.00, 1),
    ('chicken',     'Whole Chicken',       75.00, 1)
ON DUPLICATE KEY UPDATE name = VALUES(name);
