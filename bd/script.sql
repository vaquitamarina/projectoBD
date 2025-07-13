CREATE DATABASE IF NOT EXISTS SistemaTransporte;
USE SistemaTransporte;

CREATE TABLE IF NOT EXISTS usuario (
    idUsuario INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(10) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS pasajero (
    idPasajero INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(50) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    fechaDeNacimiento DATE,
    sexo CHAR(1) NOT NULL,
    dni CHAR(8) UNIQUE
);
CREATE TABLE IF NOT EXISTS bus (
    idBus INT AUTO_INCREMENT PRIMARY KEY,
    placa VARCHAR(10) UNIQUE NOT NULL,
    clase VARCHAR(20) NOT NULL,
    estado VARCHAR(20) NOT NULL,
    nAsientos INT NOT NULL
);

CREATE TABLE IF NOT EXISTS trabajador (
    idTrabajador INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(50) NOT NULL,
    apellidos VARCHAR(50) NOT NULL,
    sexo CHAR(1) NOT NULL,
    dni CHAR(8) UNIQUE
);

CREATE TABLE IF NOT EXISTS boletero (
    idTrabajador INT PRIMARY KEY,
    FOREIGN KEY (idTrabajador) REFERENCES Trabajador(idTrabajador)
);

CREATE TABLE IF NOT EXISTS trabajadorBus (
    idTrabajador INT PRIMARY KEY,
    idBus INT NOT NULL,
    FOREIGN KEY (idTrabajador) REFERENCES trabajador(idTrabajador),
    FOREIGN KEY (idBus) REFERENCES bus(idBus)
);

CREATE TABLE IF NOT EXISTS chofer(
    idTrabajador INT PRIMARY KEY,
    licencia CHAR(9) NOT NULL,
    FOREIGN KEY (idTrabajador) REFERENCES trabajadorBus(idTrabajador)
);

CREATE TABLE IF NOT EXISTS terramozo(
    idTrabajador INT PRIMARY KEY,
    FOREIGN KEY (idTrabajador) REFERENCES trabajadorBus(idTrabajador) 
);


CREATE TABLE IF NOT EXISTS viaje (
    idViaje INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    hInicio TIME NOT NULL,
    hFinal TIME NOT NULL
);

CREATE TABLE IF NOT EXISTS asiento (
    idAsiento INT AUTO_INCREMENT PRIMARY KEY,
    numeroAsiento INT NOT NULL,
    piso INT NOT NULL,
    estado CHAR(1) NOT NULL,
    idBus INT NOT NULL,
    FOREIGN KEY (idBus) REFERENCES bus(idBus)
);

CREATE TABLE IF NOT EXISTS ruta (
    idRuta INT AUTO_INCREMENT PRIMARY KEY,
    ciudadOrigen VARCHAR(50) NOT NULL,
    ciudadFinal VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS ticket (
    idTicket INT AUTO_INCREMENT PRIMARY KEY,
    idPasajero INT NOT NULL,
    idViaje INT NOT NULL, 
    idAsiento INT NOT NULL,
    idBoletero INT,
    idUsuario INT,
    FOREIGN KEY (idPasajero) REFERENCES pasajero(idPasajero),
    FOREIGN KEY (idViaje) REFERENCES viaje(idViaje),
    FOREIGN KEY (idAsiento) REFERENCES asiento(idAsiento),
    FOREIGN KEY (idBoletero) REFERENCES boletero(idTrabajador),
    FOREIGN KEY (idUsuario) REFERENCES usuario(idUsuario),
    CONSTRAINT chk_comprador CHECK ( 
        (idBoletero IS NOT NULL AND idUsuario IS NULL) OR
        (idBoletero IS NULL AND idUsuario IS NOT NULL)
    )
);

CREATE TABLE IF NOT EXISTS viajeRuta (
    idViaje INT NOT NULL,
    idRutas INT NOT NULL,
    PRIMARY KEY (idViaje, idRutas), 
    FOREIGN KEY (idViaje) REFERENCES viaje(idViaje),
    FOREIGN KEY (idRutas) REFERENCES ruta(idRutas)
);

CREATE TABLE IF NOT EXISTS viajeBus (
    idViaje INT NOT NULL,
    idBus INT NOT NULL,
    PRIMARY KEY (idViaje, idBus),
    FOREIGN KEY (idViaje) REFERENCES viaje(idViaje),
    FOREIGN KEY (idBus) REFERENCES bus(idBus)
);
