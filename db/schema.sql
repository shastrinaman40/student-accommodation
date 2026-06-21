-- Database: student_accommodation

DROP TABLE IF EXISTS interested_users;
DROP TABLE IF EXISTS property_amenities;
DROP TABLE IF EXISTS amenities;
DROP TABLE IF EXISTS properties;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(30)
);

CREATE TABLE properties (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  city VARCHAR(100) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  gender ENUM('Male','Female','Unisex') DEFAULT 'Unisex',
  rating DECIMAL(2,1) DEFAULT 0.0,
  description TEXT,
  images TEXT
);

CREATE TABLE amenities (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL
);

CREATE TABLE property_amenities (
  property_id INT,
  amenity_id INT,
  PRIMARY KEY(property_id, amenity_id),
  FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
  FOREIGN KEY (amenity_id) REFERENCES amenities(id) ON DELETE CASCADE
);

CREATE TABLE interested_users (
  user_id INT,
  property_id INT,
  PRIMARY KEY(user_id, property_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample data
INSERT INTO users (name, email, password, phone) VALUES
('Alice Student','alice@example.com', '$2y$10$placeholder', '1234567890');

INSERT INTO properties (name, city, price, gender, rating, description, images) VALUES
('Maple Residency','Delhi', 7000.00, 'Female', 4.5, 'Close to university with AC rooms', 'uploads/maple1.jpg,uploads/maple2.jpg'),
('College Home PG','Mumbai', 5000.00, 'Male', 4.0, 'Affordable PG near college', 'uploads/college1.jpg'),
('Green Stay','Bangalore', 8000.00, 'Unisex', 4.7, 'Modern amenities and good food', 'uploads/green1.jpg,uploads/green2.jpg');

INSERT INTO amenities (name) VALUES ('WiFi'), ('AC'), ('Laundry'), ('Meals'), ('Hot Water');

INSERT INTO property_amenities (property_id, amenity_id) VALUES
(1,1),(1,2),(1,4),(2,1),(2,3),(3,1),(3,2),(3,3),(3,5);

-- Note: replace the placeholder password with hashed passwords when creating real users.
