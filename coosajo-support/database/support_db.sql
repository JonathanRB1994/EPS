CREATE DATABASE support_db;
USE support_db;

CREATE TABLE users(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(16) NOT NULL UNIQUE,
    role VARCHAR(16) NOT NULL DEFAULT 'technical',
    password VARCHAR(60) NOT NULL,
    fullname VARCHAR(100) NOT NULL
);

INSERT INTO users(username, password, role, fullname) VALUE ('admin', 'admin', 'admin','Administrador de soporte técnico');

CREATE TABLE support(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(150) NOT NULL,
    id_incidencia INT NOT NULL DEFAULT 100,    
    nombre_incidencia VARCHAR(150) NOT NULL DEFAULT 'Otros',
    id_tipo_incidencia INT NOT NULL DEFAULT 100,
    nombre_tipo_incidencia VARCHAR(150) NOT NULL DEFAULT 'Otros',
    views INT NOT NULL DEFAULT 0,
    date_create DATETIME NOT NULL DEFAULT NOW(),
    description TEXT    
);

CREATE TABLE step(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    step INT NOT NULL,
    title VARCHAR(150) NOT NULL, 
    image TEXT,
    description TEXT,    
    support_id INT NOT NULL,    
    CONSTRAINT fk_step FOREIGN KEY (support_id) REFERENCES support(id)
);


