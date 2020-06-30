CREATE DATABASE support_db;
USE support_db;

-- USERS TABLE
CREATE TABLE users(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(16) NOT NULL UNIQUE,
    role VARCHAR(16) NOT NULL DEFAULT 'technical',
    password VARCHAR(60) NOT NULL,
    fullname VARCHAR(100) NOT NULL
);

INSERT INTO users(username, password, fullname) VALUE ('admin', 'admin', 'Administrador de soporte tpecnico');

DESCRIBE users;

-- PROBLEM
CREATE TABLE support(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(150) NOT NULL,
    description TEXT
);

INSERT INTO support(title, description) VALUE ('Impresora', 'Resolver problemas relacionados con la impresora.');

DESCRIBE support;

-- LINKS TABLE
CREATE TABLE step(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    step INT NOT NULL,
    title VARCHAR(150) NOT NULL, 
    image TEXT,
    description TEXT,    
    support_id INT NOT NULL,    
    CONSTRAINT fk_step FOREIGN KEY (support_id) REFERENCES support(id)
);

INSERT INTO step(step, title, description, support_id) VALUE
    (1, 'Apagar la impresora', 'Sigue el cable de la impresora que va al tomacorriente y desconectalo.',1),
    (2, 'Conecta la impresora', 'Conecta el cable que as desconectado en el paso anterior.',1),
    (3, 'Apagar la impresora', 'Revisa la luces que da la impresora, al finale debe dar una luz verde.',1);

DESCRIBE step;




-- SIMULACION DB TICKET
create table ticket(
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    token VARCHAR(100) NOT NULL,
    description TEXT
);

DESCRIBE ticket;

INSERT INTO ticket(token, description) VALUES ("ABC123","Pendiente de corrección");