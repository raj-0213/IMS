CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone_no VARCHAR(20),
    address TEXT,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by INT,
    deleted_at TIMESTAMP,
    deleted_by INT
);


CREATE TABLE draft_products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    sales_price DECIMAL(10, 2) NOT NULL,
    mrp DECIMAL(10, 2) NOT NULL,
    manufacturer_name VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    is_banned BOOLEAN DEFAULT FALSE,
    is_discontinued BOOLEAN DEFAULT FALSE,
    is_assured BOOLEAN DEFAULT FALSE,
    is_refrigerated BOOLEAN DEFAULT FALSE,
    category_id INT UNSIGNED NOT NULL,
    product_status ENUM('Draft', 'Published', 'Unpublished') DEFAULT 'Draft',
    ws_code VARCHAR(255) DEFAULT 'null',
    combination TEXT NOT NULL,
    published_by INT UNSIGNED,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    published_at TIMESTAMP NULL DEFAULT NULL,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    created_by INT UNSIGNED NOT NULL,
    updated_by INT UNSIGNED,
    deleted_by INT UNSIGNED,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (published_by) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id),
    FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE RESTRICT
);


CREATE TABLE published_products (
    id SERIAL PRIMARY KEY,
    ws_code INT UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    sales_price FLOAT NOT NULL,
    mrp FLOAT NOT NULL,
    manufacturer_name VARCHAR(255) NOT NULL,
    is_banned BOOLEAN DEFAULT FALSE,
    is_discontinued BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    is_assured BOOLEAN DEFAULT FALSE,
    is_refrigerated BOOLEAN DEFAULT FALSE,
    category_id INT UNSIGNED NOT NULL,
    combination TEXT NOT NULL,
    created_by INT UNSIGNED NOT NULL,
    updated_by INT UNSIGNED,
    deleted_by INT UNSIGNED,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id),
    FOREIGN KEY (deleted_by) REFERENCES users(id)
);

CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    category_name VARCHAR(255) NOT NULL,
    is_deleted BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by INT,
    deleted_at TIMESTAMP,
    deleted_by INT
);

CREATE TABLE molecules (
    id SERIAL PRIMARY KEY,
    molecule_name VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by INT,
    deleted_at TIMESTAMP,
    deleted_by INT
);


CREATE TABLE product_molecules (
    id SERIAL PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    molecule_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (molecule_id) REFERENCES molecules(id)
    FOREIGN KEY (product_id) REFERENCES draft_products(id)
);