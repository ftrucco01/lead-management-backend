CREATE TABLE IF NOT EXISTS leads (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  phone VARCHAR(20),
  source ENUM('facebook', 'google', 'linkedin', 'manual') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);