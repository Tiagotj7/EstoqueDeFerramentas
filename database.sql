CREATE DATABASE IF NOT EXISTS tool_inventory
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE tool_inventory;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE tools (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  category VARCHAR(80) NOT NULL,
  unit VARCHAR(30) NOT NULL,
  min_stock INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE stock_moves (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tool_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  move_type ENUM('IN','OUT') NOT NULL,
  quantity INT NOT NULL,
  note VARCHAR(255) NULL,
  moved_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_stock_moves_tool
    FOREIGN KEY (tool_id) REFERENCES tools(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_stock_moves_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT chk_stock_moves_quantity CHECK (quantity > 0)
) ENGINE=InnoDB;

CREATE INDEX idx_stock_moves_tool_id ON stock_moves(tool_id);
CREATE INDEX idx_stock_moves_user_id ON stock_moves(user_id);
CREATE INDEX idx_stock_moves_moved_at ON stock_moves(moved_at);

ALTER TABLE users ADD COLUMN status TINYINT(1) NOT NULL DEFAULT 1;
ALTER TABLE tools ADD COLUMN status TINYINT(1) NOT NULL DEFAULT 1;

CREATE INDEX idx_users_status ON users(status);
CREATE INDEX idx_tools_status ON tools(status);