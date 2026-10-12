CREATE DATABASE IF NOT EXISTS ibuzkenya_pos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ibuzkenya_pos;

CREATE TABLE users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(50) UNIQUE NOT NULL,
 password_hash VARCHAR(255) NOT NULL,
 full_name VARCHAR(100) NOT NULL,
 role ENUM('admin','cashier','cyber') NOT NULL DEFAULT 'cashier',
 active TINYINT(1) NOT NULL DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE settings (
 id INT PRIMARY KEY DEFAULT 1,
 business_name VARCHAR(150) NOT NULL DEFAULT 'IBUZKENYA',
 phone VARCHAR(50) DEFAULT '',
 email VARCHAR(150) DEFAULT '',
 address VARCHAR(255) DEFAULT '',
 receipt_footer VARCHAR(255) DEFAULT 'Thank you for your business.'
);

CREATE TABLE customers (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 phone VARCHAR(50) DEFAULT '',
 email VARCHAR(150) DEFAULT '',
 address VARCHAR(255) DEFAULT '',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(150) NOT NULL,
 sku VARCHAR(80) UNIQUE,
 category VARCHAR(100) DEFAULT 'General',
 buying_price DECIMAL(12,2) NOT NULL DEFAULT 0,
 selling_price DECIMAL(12,2) NOT NULL DEFAULT 0,
 stock_qty DECIMAL(12,2) NOT NULL DEFAULT 0,
 reorder_level DECIMAL(12,2) NOT NULL DEFAULT 5,
 unit VARCHAR(30) DEFAULT 'pcs',
 active TINYINT(1) DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE services (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(150) NOT NULL,
 category VARCHAR(100) DEFAULT 'Cyber',
 price DECIMAL(12,2) NOT NULL DEFAULT 0,
 active TINYINT(1) DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE sales (
 id INT AUTO_INCREMENT PRIMARY KEY,
 receipt_no VARCHAR(40) UNIQUE NOT NULL,
 customer_id INT NULL,
 user_id INT NOT NULL,
 subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
 discount DECIMAL(12,2) NOT NULL DEFAULT 0,
 total DECIMAL(12,2) NOT NULL DEFAULT 0,
 amount_paid DECIMAL(12,2) NOT NULL DEFAULT 0,
 balance DECIMAL(12,2) NOT NULL DEFAULT 0,
 payment_method ENUM('cash','mpesa','bank','credit','partial') NOT NULL DEFAULT 'cash',
 sale_type ENUM('product','service','mixed') DEFAULT 'product',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE SET NULL,
 FOREIGN KEY(user_id) REFERENCES users(id)
);

CREATE TABLE sale_items (
 id INT AUTO_INCREMENT PRIMARY KEY,
 sale_id INT NOT NULL,
 item_type ENUM('product','service') NOT NULL,
 item_id INT NOT NULL,
 item_name VARCHAR(180) NOT NULL,
 qty DECIMAL(12,2) NOT NULL DEFAULT 1,
 unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,
 buying_price DECIMAL(12,2) NOT NULL DEFAULT 0,
 line_total DECIMAL(12,2) NOT NULL DEFAULT 0,
 FOREIGN KEY(sale_id) REFERENCES sales(id) ON DELETE CASCADE
);

CREATE TABLE stock_movements (
 id INT AUTO_INCREMENT PRIMARY KEY,
 product_id INT NOT NULL,
 movement_type ENUM('opening','purchase','sale','adjustment') NOT NULL,
 qty DECIMAL(12,2) NOT NULL,
 unit_cost DECIMAL(12,2) DEFAULT 0,
 reference VARCHAR(100) DEFAULT '',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE expenses (
 id INT AUTO_INCREMENT PRIMARY KEY,
 title VARCHAR(150) NOT NULL,
 category VARCHAR(100) DEFAULT 'General',
 amount DECIMAL(12,2) NOT NULL,
 payment_method ENUM('cash','mpesa','bank') DEFAULT 'cash',
 notes TEXT,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE payments (
 id INT AUTO_INCREMENT PRIMARY KEY,
 sale_id INT NOT NULL,
 payment_method ENUM('cash','mpesa','bank') NOT NULL,
 amount DECIMAL(12,2) NOT NULL,
 reference VARCHAR(100) DEFAULT '',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(sale_id) REFERENCES sales(id) ON DELETE CASCADE
);

INSERT INTO settings VALUES (1,'IBUZKENYA','0719477726','ibuzkenya@gmail.com','Kenya','Thank you for your business.');

INSERT INTO users(username,password_hash,full_name,role) VALUES
('admin',SHA2('admin123',256),'Administrator','admin'),
('cashier',SHA2('cashier123',256),'Cashier','cashier'),
('cyber',SHA2('cyber123',256),'Cyber Attendant','cyber');

INSERT INTO services(name,category,price) VALUES
('KRA Services','Government',100),
('SHA Services','Government',100),
('NSSF Services','Government',100),
('NTSA Services','Government',100),
('eCitizen Services','Government',100),
('HELB Services','Government',100),
('KUCCPS Services','Government',100),
('Passport Services','Government',300),
('Good Conduct','Government',300),
('Business Registration','Government',500),
('CV','Document',200),
('Printing','Printing',10),
('Photocopy','Printing',5),
('Scanning','Printing',20),
('Typesetting','Document',50),
('Passport Photos','Photography',100),
('Design Services','Design',300),
('Custom Services','Cyber',100);

INSERT INTO products(name,sku,category,buying_price,selling_price,stock_qty,reorder_level,unit) VALUES
('A4 Printing Paper','PAPER-A4','Stationery',500,650,10,2,'ream'),
('USB Flash Disk 32GB','USB32','Accessories',600,900,5,2,'pcs'),
('Ball Pen','PEN-BLUE','Stationery',10,20,30,10,'pcs');
