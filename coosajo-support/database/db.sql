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
    keywords TEXT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT
);

INSERT INTO support(title, keywords, description) VALUE ('Impresora', 'Impresora no enciende conecta imprime', 'Resolver problemas relacionados con la impresora.');

DESCRIBE support;

-- LINKS TABLE
CREATE TABLE step(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    step INT NOT NULL,
    title VARCHAR(150) NOT NULL, 
    img VARCHAR(255),
    description TEXT,    
    support_id INT NOT NULL,    
    CONSTRAINT fk_step FOREIGN KEY (support_id) REFERENCES support(id)
);

INSERT INTO step(step, title, description, support_id) VALUE
    (1, 'Apagar la impresora', 'Sigue el cable de la impresora que va al tomacorriente y desconectalo.',1),
    (2, 'Conecta la impresora', 'Conecta el cable que as desconectado en el paso anterior.',1),
    (3, 'Apagar la impresora', 'Revisa la luces que da la impresora, al finale debe dar una luz verde.',1);

DESCRIBE step;
