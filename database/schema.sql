CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  firstname VARCHAR(100),
  email VARCHAR(255),
  password VARCHAR(255),
  phone_number VARCHAR(20),
  birthdate DATE,
  account_creation_date DATE,
  account_status INT,
  profile_picture TEXT,
  is_admin INT
);
