-- Active: 1752802333437@@127.0.0.1@3306@sistematransporte

-- 📌 USUARIO
INSERT INTO usuario (username, password, email) VALUES
('admin', 'admin123', 'admin@transportes.com'),
('usuario1', 'pass123', 'usuario1@email.com'),
('cliente1', 'client123', 'cliente1@gmail.com'),
('vendedor1', 'vend123', 'vendedor1@transportes.com'),
('supervisor', 'super123', 'supervisor@transportes.com');

-- 📌 PASAJERO
INSERT INTO pasajero (nombres, apellidos, email, fechaDeNacimiento, sexo, dni) VALUES
('Ana', 'López Martínez', 'ana.lopez@email.com', '1990-05-15', 'F', '12345671'),
('Pedro', 'Rodríguez Silva', 'pedro.rodriguez@gmail.com', '1985-03-22', 'M', '12345672'),
('Carmen', 'Vásquez Torres', 'carmen.vasquez@hotmail.com', '1992-08-10', 'F', '12345673'),
('Luis', 'Mendoza Castro', 'luis.mendoza@yahoo.com', '1988-12-05', 'M', '12345674'),
('Rosa', 'Flores Díaz', 'rosa.flores@email.com', '1995-01-18', 'F', '12345675'),
('Miguel', 'Huanca Quispe', 'miguel.huanca@gmail.com', '1980-07-30', 'M', '12345676'),
('Elena', 'Vargas Morales', 'elena.vargas@outlook.com', '1993-11-12', 'F', '12345677'),
('Roberto', 'Espinoza León', 'roberto.espinoza@email.com', '1987-04-25', 'M', '12345678');

-- 📌 BUS
INSERT INTO bus (placa, clase, estado, nAsientos) VALUES
('ABC123', 'VIP', 'Disponible', 40),           -- idBus = 1
('XYZ789', 'Económico', 'Mantenimiento', 30),  -- idBus = 2
('LMN456', 'Premium', 'Disponible', 50),       -- idBus = 3
('DEF321', 'VIP', 'Disponible', 45),           -- idBus = 4
('GHI654', 'Económico', 'Disponible', 35),     -- idBus = 5
('JKL987', 'Premium', 'En Viaje', 48),         -- idBus = 6
('MNO147', 'VIP', 'Disponible', 42),           -- idBus = 7
('PQR258', 'Económico', 'Mantenimiento', 28);  -- idBus = 8

-- 📌 TRABAJADOR
INSERT INTO trabajador (nombres, apellidos, sexo, dni) VALUES
('Juan', 'Pérez Gonzales', 'M', '12345678'),    -- idTrabajador = 1
('María', 'García López', 'F', '87654321'),     -- idTrabajador = 2
('Carlos', 'Sánchez Díaz', 'M', '11223344'),    -- idTrabajador = 3
('Lucía', 'Torres Mendoza', 'F', '44332211'),   -- idTrabajador = 4
('José', 'Ramírez Castro', 'M', '55566677'),    -- idTrabajador = 5
('Ana', 'Morales Vega', 'F', '77788899'),       -- idTrabajador = 6
('Diego', 'Herrera Silva', 'M', '99900011'),    -- idTrabajador = 7
('Patricia', 'Jiménez Ramos', 'F', '11122233'), -- idTrabajador = 8
('Manuel', 'Vargas Torres', 'M', '33344455'),   -- idTrabajador = 9
('Sofia', 'Cruz Flores', 'F', '55566600');      -- idTrabajador = 10

-- 📌 TRABAJADOR BUS (Asigna trabajadores a buses)
INSERT INTO trabajadorBus (idTrabajador, idBus) VALUES
(1, 1), -- Juan -> bus 1
(2, 2), -- María -> bus 2
(3, 3), -- Carlos -> bus 3
(4, 1), -- Lucía -> bus 1
(5, 4), -- José -> bus 4
(6, 5), -- Ana -> bus 5
(7, 6), -- Diego -> bus 6
(8, 7), -- Patricia -> bus 7
(9, 8), -- Manuel -> bus 8
(10, 4); -- Sofia -> bus 4

-- 📌 BOLETERO
INSERT INTO boletero (idTrabajador) VALUES
(4),  -- Lucía
(6),  -- Ana
(8),  -- Patricia
(10); -- Sofia

-- 📌 CHOFER
INSERT INTO chofer (idTrabajador, licencia) VALUES
(1, 'B12345678'),  -- Juan
(3, 'C87654321'),  -- Carlos
(5, 'A11223344'),  -- José
(7, 'B55566677'),  -- Diego
(9, 'C99900011');  -- Manuel

-- 📌 TERRAMOZO
INSERT INTO terramozo (idTrabajador) VALUES
(2); -- María

-- 📌 VIAJE (con campo precio agregado)
INSERT INTO viaje (fecha, hInicio, hFinal, precio) VALUES
('2025-07-15', '08:00:00', '12:00:00', 85),  -- idViaje = 1 (Tacna-Arequipa)
('2025-07-16', '14:00:00', '20:00:00', 120), -- idViaje = 2 (Lima-Ica)
('2025-07-17', '06:00:00', '10:30:00', 45),  -- idViaje = 3 (Moquegua-Tacna)
('2025-07-18', '09:30:00', '15:00:00', 150), -- idViaje = 4 (Lima-Arequipa)
('2025-07-19', '07:00:00', '11:45:00', 95),  -- idViaje = 5 (Arequipa-Cusco)
('2025-07-20', '16:00:00', '22:30:00', 180), -- idViaje = 6 (Lima-Cusco)
('2025-07-21', '05:30:00', '09:00:00', 65),  -- idViaje = 7 (Ica-Lima)
('2025-07-22', '13:00:00', '18:30:00', 110); -- idViaje = 8 (Cusco-Arequipa)

-- 📌 RUTA
INSERT INTO ruta (ciudadOrigen, ciudadFinal) VALUES
('Tacna', 'Arequipa'),        -- idRuta = 1
('Lima', 'Ica'),              -- idRuta = 2
('Moquegua', 'Tacna'),        -- idRuta = 3
('Lima', 'Arequipa'),         -- idRuta = 4
('Arequipa', 'Cusco'),        -- idRuta = 5
('Lima', 'Cusco'),            -- idRuta = 6
('Ica', 'Lima'),              -- idRuta = 7
('Cusco', 'Arequipa'),        -- idRuta = 8
('Arequipa', 'Tacna'),        -- idRuta = 9
('Cusco', 'Lima');            -- idRuta = 10

--  VIAJE RUTA
INSERT INTO viajeRuta (idViaje, idRuta) VALUES
(1, 1),  -- viaje 1: Tacna - Arequipa
(2, 2),  -- viaje 2: Lima - Ica
(3, 3),  -- viaje 3: Moquegua - Tacna
(4, 4),  -- viaje 4: Lima - Arequipa
(5, 5),  -- viaje 5: Arequipa - Cusco
(6, 6),  -- viaje 6: Lima - Cusco
(7, 7),  -- viaje 7: Ica - Lima
(8, 8);  -- viaje 8: Cusco - Arequipa

--  VIAJE BUS
INSERT INTO viajeBus (idViaje, idBus) VALUES
(1, 1), -- viaje 1 con bus 1
(2, 2), -- viaje 2 con bus 2
(3, 3), -- viaje 3 con bus 3
(4, 4), -- viaje 4 con bus 4
(5, 5), -- viaje 5 con bus 5
(6, 6), -- viaje 6 con bus 6
(7, 7), -- viaje 7 con bus 7
(8, 1); -- viaje 8 con bus 1

--  ASIENTOS por bus (5 asientos por bus para mejor ejemplo)
-- Bus 1 (ABC123) - VIP
INSERT INTO asiento (numeroAsiento, piso, estado, idBus) VALUES
(1, 1, '0', 1), (2, 1, '0', 1), (3, 1, '0', 1), (4, 1, '0', 1), (5, 2, '0', 1);

-- Bus 2 (XYZ789) - Económico
INSERT INTO asiento (numeroAsiento, piso, estado, idBus) VALUES
(1, 1, '0', 2), (2, 1, '0', 2), (3, 1, '0', 2), (4, 1, '0', 2), (5, 1, '0', 2);

-- Bus 3 (LMN456) - Premium
INSERT INTO asiento (numeroAsiento, piso, estado, idBus) VALUES
(1, 1, '0', 3), (2, 1, '0', 3), (3, 1, '0', 3), (4, 2, '0', 3), (5, 2, '0', 3);

-- Bus 4 (DEF321) - VIP
INSERT INTO asiento (numeroAsiento, piso, estado, idBus) VALUES
(1, 1, '0', 4), (2, 1, '0', 4), (3, 1, '0', 4), (4, 2, '0', 4), (5, 2, '0', 4);

-- Bus 5 (GHI654) - Económico
INSERT INTO asiento (numeroAsiento, piso, estado, idBus) VALUES
(1, 1, '0', 5), (2, 1, '0', 5), (3, 1, '0', 5), (4, 1, '0', 5), (5, 1, '0', 5);

-- Bus 6 (JKL987) - Premium
INSERT INTO asiento (numeroAsiento, piso, estado, idBus) VALUES
(1, 1, '1', 6), (2, 1, '0', 6), (3, 1, '1', 6), (4, 2, '0', 6), (5, 2, '1', 6);

-- Bus 7 (MNO147) - VIP
INSERT INTO asiento (numeroAsiento, piso, estado, idBus) VALUES
(1, 1, '0', 7), (2, 1, '0', 7), (3, 1, '0', 7), (4, 2, '0', 7), (5, 2, '0', 7);

-- Bus 8 (PQR258) - Económico
INSERT INTO asiento (numeroAsiento, piso, estado, idBus) VALUES
(1, 1, '0', 8), (2, 1, '0', 8), (3, 1, '0', 8), (4, 1, '0', 8), (5, 1, '0', 8);

-- 📌 TICKETS (algunos ejemplos)
INSERT INTO ticket (idPasajero, idViaje, idAsiento, idBoletero, idUsuario) VALUES
(1, 1, 1, 4, NULL),    -- Ana compra ticket con boletero Lucía
(2, 1, 3, NULL, 2),    -- Pedro compra online
(3, 2, 6, 6, NULL),    -- Carmen compra con boletero Ana
(4, 3, 11, 8, NULL),   -- Luis compra con boletero Patricia
(5, 4, 16, NULL, 3),   -- Rosa compra online
(6, 5, 21, 10, NULL),  -- Miguel compra con boletero Sofia
(7, 6, 26, NULL, 4),   -- Elena compra online
(8, 7, 31, 4, NULL);   -- Roberto compra con boletero Lucía