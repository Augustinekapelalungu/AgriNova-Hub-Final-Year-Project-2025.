-- Use your database
USE agrinova_hub;

-- --- USERS TABLE ---
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    user_type ENUM('customer', 'farmer', 'dealer') NOT NULL,
    newsletter BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- If table exists but has #1932, discard and import tablespace
ALTER TABLE users DISCARD TABLESPACE;
-- Copy the old users.ibd file into the users table folder
ALTER TABLE users IMPORT TABLESPACE;

-- --- CATEGORIES TABLE ---
CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

ALTER TABLE categories DISCARD TABLESPACE;
-- Copy categories.ibd file into categories folder
ALTER TABLE categories IMPORT TABLESPACE;

-- --- PRODUCTS TABLE ---
CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT NOT NULL,
    category_id INT,
    image_url VARCHAR(255),
    farmer_id INT,
    discount_percentage DECIMAL(5,2) DEFAULT 0,
    discount_price DECIMAL(10,2),
    discount_end_date DATE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id),
    FOREIGN KEY (farmer_id) REFERENCES users(user_id)
) ENGINE=InnoDB;

ALTER TABLE products DISCARD TABLESPACE;
-- Copy products.ibd file into products folder
ALTER TABLE products IMPORT TABLESPACE;

-- --- ORDERS TABLE ---
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50),
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
) ENGINE=InnoDB;

ALTER TABLE orders DISCARD TABLESPACE;
-- Copy orders.ibd file into orders folder
ALTER TABLE orders IMPORT TABLESPACE;

-- --- ORDER ITEMS TABLE ---
CREATE TABLE IF NOT EXISTS order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    product_name VARCHAR(100),
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
) ENGINE=InnoDB;

ALTER TABLE order_items DISCARD TABLESPACE;
-- Copy order_items.ibd file into order_items folder
ALTER TABLE order_items IMPORT TABLESPACE;

-- --- CONTACT MESSAGES TABLE ---
CREATE TABLE IF NOT EXISTS contact_messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

ALTER TABLE contact_messages DISCARD TABLESPACE;
-- Copy contact_messages.ibd file into contact_messages folder
ALTER TABLE contact_messages IMPORT TABLESPACE;
