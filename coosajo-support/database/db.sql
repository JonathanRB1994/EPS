CREATE DATABASE support_db;
USE support_db;

-- USERS TABLE
CREATE TABLE users(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(16) NOT NULL,
    password VARCHAR(60) NOT NULL,
    fullname VARCHAR(100) NOT NULL
);

INSERT INTO users(username, password, fullname) VALUE ('jonathan', 'jonathan', 'Jonathan Kevin Adalberto Rojas Bollat');

DESCRIBE users;

-- PROBLEM
CREATE TABLE support(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(150) NOT NULL,
    description TEXT
);

DESCRIBE support;

-- LINKS TABLE
CREATE TABLE step(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(150) NOT NULL, 
    img VARCHAR(255) NOT NULL,
    description TEXT,    
    support_id INT NOT NULL,    
    CONSTRAINT fk_step FOREIGN KEY (support_id) REFERENCES support(id)
);

DESCRIBE step;
