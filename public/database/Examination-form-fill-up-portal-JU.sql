CREATE DATABASE IF NOT EXISTS exam_portal;
USE exam_portal;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('student','chairman') DEFAULT 'student'
);

CREATE TABLE forms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT,
  title VARCHAR(200),
  description TEXT,
  status ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',
  FOREIGN KEY (student_id) REFERENCES users(id)
);
