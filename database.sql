--Create database
CREATE DATABASE IF NOT EXISTS project_manager_db;
USE project_manager_db;

--Users Table
CREATE TABLE users (
    uid INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    UNIQUE (username),
    UNIQUE (email)
);

--Projects Table
CREATE TABLE projects (
    pid INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    description TEXT,
    phase ENUM('design', 'development', 'testing', 'deployment', 'complete'),
    uid INT(11),
    
--Foreign key constraint
    CONSTRAINT fk_user_project
    FOREIGN KEY (uid) REFERENCES users(uid)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);