CREATE TABLE IF NOT EXISTS bd_catalog_products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  price_usd DECIMAL(10,2) NOT NULL DEFAULT 0,
  category VARCHAR(100) NOT NULL,
  description TEXT NOT NULL,
  is_enquiry TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS bd_catalog_product_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  image_url VARCHAR(500) NOT NULL,
  sort_order INT NOT NULL DEFAULT 1,
  CONSTRAINT fk_bd_catalog_product_images_product
    FOREIGN KEY (product_id) REFERENCES bd_catalog_products(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS bd_catalog_variants (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  variant_key VARCHAR(64) NOT NULL,
  label VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  price_usd DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_bd_catalog_variants_product
    FOREIGN KEY (product_id) REFERENCES bd_catalog_products(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS bd_catalog_addons (
  id INT AUTO_INCREMENT PRIMARY KEY,
  addon_key VARCHAR(64) NOT NULL,
  name VARCHAR(255) NOT NULL,
  price_usd DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
