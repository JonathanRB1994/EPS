CREATE DATABASE ticket_db;
USE ticket_db;

CREATE TABLE incidencia_tipo(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nombre_tipo_incidencia VARCHAR(100) NOT NULL,
    nomenclatura VARCHAR(20) NOT NULL,
    estado INT NOT NULL
);

CREATE TABLE incidencia(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nombre_incidencia VARCHAR(100) NOT NULL,
    estado INT NOT NULL,
    id_incidencia_tipo INT NOT NULL,
    CONSTRAINT fk_incidencia FOREIGN KEY (id_incidencia_tipo) REFERENCES incidencia_tipo(id)
);


create table ticket(
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    cif VARCHAR(50), 	   
	cif_asignado VARCHAR(50),   
	id_tipo_incidencia INT,
    id_incidencia INT,	
	otra_incidencia VARCHAR(250),
    descripcion TEXT,	
    fecha_creacion DATETIME,
	id_estado INT,
	id_subestado INT
);


CREATE TABLE ticket_estado (
	id_estado INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	nombre_estado VARCHAR(50)
);

create table ticket_subestado (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	id_ticket_estado INT NOT NULL, 
	nombre_subestado VARCHAR(50)
);

DELETE FROM `ticket_estado`;

INSERT INTO `ticket_estado` (`id_estado`, `nombre_estado`) VALUES
	(2, 'Cerrado'),
	(1, 'Abierto');

DELETE FROM `ticket_subestado`;

INSERT INTO `ticket_subestado` (`id`, `id_ticket_estado`, `nombre_subestado`) VALUES
	(7, 2, 'No Aplicaba'),
	(6, 2, 'Insatisfactoriamente'),
	(5, 2, 'Satisfactoriamente'),
	(4, 2, 'Inmediatamente'),
	(3, 1, 'En Espera'),
	(2, 1, 'Asignado'),
	(1, 1, 'Sin Asignar');


DELETE FROM `incidencia_tipo`;

INSERT INTO `incidencia_tipo` (`id`, `nombre_tipo_incidencia`, `nomenclatura`, `estado`) VALUES
	(4, 'Seguridad', 'SEG', 1),
	(2, 'Desarrollo', 'DES', 1),
	(3, 'Infraestructura', 'INF', 1),
	(1, 'Soporte Técnico', 'STE', 1);

DELETE FROM `incidencia`;

INSERT INTO `incidencia` (`id`, `nombre_incidencia`, `estado`, `id_incidencia_tipo`) VALUES
	(23, 'VPN', 1, 4),
	(22, 'Mensajes de Seguridad', 1, 4),
	(21, 'Antivirus', 1, 4),
	(20, 'Fortinet', 1, 4),
	(19, 'Permisos Enlaces', 1, 4),
	(18, 'Permisos Web', 1, 4),
	(17, 'Planta Telefónica', 1, 3),
	(15, 'Servidores', 1, 3),
	(16, 'Redes', 1, 3),
	(14, 'Enlaces', 1, 3),
	(13, 'Problema Internet', 1, 1),
	(12, 'Configuración periféricos', 1, 1),
	(10, 'Problema Bankworks', 1, 1),
	(11, 'Problema Portal', 1, 1),
	(9, 'Telefonía', 1, 1),
	(8, 'Modificar datos en BBDD', 1, 2),
	(7, 'Modificaciones Menores en APPs', 1, 2),
	(6, 'Creación de Usuarios', 1, 2),
	(5, 'Acceso a Portal', 1, 2),
	(4, 'Revisión de Equipo', 1, 1),
	(3, 'Correo', 1, 1),
	(2, 'Configuración de aplicativos', 1, 1),
	(1, 'Activación Software', 1, 1),
	(24, 'Reseteo Contraseña Portal', 1, 1),
	(25, 'Configuraciones TEAMS', 1, 1);

