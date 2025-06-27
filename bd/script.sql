CREATE DATABASE IF NOT EXISTS SistemaTransporte;
USE SistemaTransporte;

CREATE TABLE IF NOT EXISTS usuario (
    idUsuario INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(10) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
);

CREATE TABLE IF NOT EXISTS pasajero (
    idPasajero INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(50) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    fechaDeNacimiento DATE,
    sexo CHAR(1),
    dni CHAR(8) UNIQUE,   
);

CREATE TABLE IF NOT EXISTS trabajador (
    idTrabajador INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(50) NOT NULL,
    apellidos VARCHAR(50) NOT NULL,
    sexo CHAR(1) NOT NULL,
    dni CHAR(8) UNIQUE,
);

CREATE TABLE IF NOT EXISTS bus (
    placa VARCHAR(10) AUTO_INCREMENT PRIMARY KEY,
    clase VARCHAR(20) NOT NULL,
    estado VARCHAR(20) NOT NULL,
    nAsientos INT NOT NULL, 
);

CREATE TABLE IF NOT EXISTS viaje (
    idViaje INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    hInicio TIME NOT NULL,
    hFinal TIME NOT NULL,
);

CREATE TABLE IF NOT EXISTS asiento (
    idAsiento INT AUTO_INCREMENT PRIMARY KEY,
    numeroAsiento INT NOT NULL,
    piso INT NOT NULL,
    estado CHAR(1) NOT NULL,
    placaBus VARCHAR(10),
);

CREATE TABLE IF NOT EXISTS rutas (
    idRutas INT AUTO_INCREMENT PRIMARY KEY,
    ciudadOrigen VARCHAR(50) NOT NULL,
    ciudadFinal VARCHAR(50) NOT NULL,
);

CREATE TABLE IF NOT EXISTS ticket (
    idTicket INT AUTO_INCREMENT PRIMARY KEY,
    idPasajero INT,
    CONSTRAINT fk_pasajero
    FOREIGN KEY (idPasajero)
    REFERENCES pasajero(idPasajero)
    
);