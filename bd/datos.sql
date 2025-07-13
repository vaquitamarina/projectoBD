-- Active: 1752375211800@@127.0.0.1@3306@SistemaTransporte
-- 📌 BUS
INSERT INTO bus (placa, clase, estado, nAsientos) VALUES
('ABC123', 'VIP', 'Disponible', 40),       -- idBus = 1
('XYZ789', 'Económico', 'Mantenimiento', 30), -- idBus = 2
('LMN456', 'Premium', 'Disponible', 50);   -- idBus = 3

-- 📌 TRABAJADOR
INSERT INTO trabajador (nombres, apellidos, sexo, dni) VALUES
('Juan', 'Pérez', 'M', '12345678'),   -- idTrabajador = 1
('María', 'García', 'F', '87654321'), -- idTrabajador = 2
('Carlos', 'Sánchez', 'M', '11223344'), -- idTrabajador = 3
('Lucía', 'Torres', 'F', '44332211');   -- idTrabajador = 4

-- 📌 TRABAJADOR BUS (Asigna a bus)
INSERT INTO trabajadorBus (idTrabajador, idBus) VALUES
(1, 1), -- Juan -> bus 1
(2, 2), -- María -> bus 2
(3, 3), -- Carlos -> bus 3
(4, 1); -- Lucía -> bus 1

-- 📌 BOLETERO
INSERT INTO boletero (idTrabajador) VALUES
(4); -- Lucía es boletera

-- 📌 CHOFER
INSERT INTO chofer (idTrabajador, licencia) VALUES
(1, 'B12345678'),  -- Juan
(3, 'C87654321');  -- Carlos

-- 📌 TERRAMOZO
INSERT INTO terramozo (idTrabajador) VALUES
(2); -- María

-- 📌 VIAJE
INSERT INTO viaje (fecha, hInicio, hFinal) VALUES
('2025-07-15', '08:00:00', '12:00:00'), -- idViaje = 1
('2025-07-16', '14:00:00', '20:00:00'), -- idViaje = 2
('2025-07-17', '06:00:00', '10:30:00'); -- idViaje = 3

-- 📌 RUTA
INSERT INTO ruta (ciudadOrigen, ciudadFinal) VALUES
('Tacna', 'Arequipa'),     -- idRuta = 1
('Lima', 'Ica'),           -- idRuta = 2
('Moquegua', 'Tacna');     -- idRuta = 3

-- 📌 VIAJE RUTA
INSERT INTO viajeRuta (idViaje, idRuta) VALUES
(1, 1),  -- viaje 1: Tacna - Arequipa
(2, 2),  -- viaje 2: Lima - Ica
(3, 3);  -- viaje 3: Moquegua - Tacna

-- 📌 VIAJE BUS
INSERT INTO viajeBus (idViaje, idBus) VALUES
(1, 1),
(2, 2),
(3, 3);

-- 📌 ASIENTOS por bus (3 asientos por bus para ejemplo)
-- Bus 1 (ABC123)
INSERT INTO asiento (numeroAsiento, piso, estado, idBus) VALUES
(1, 1, '0', 1), (2, 1, '0', 1), (3, 1, '0', 1);

-- Bus 2 (XYZ789)
INSERT INTO asiento (numeroAsiento, piso, estado, idBus) VALUES
(1, 1, '0', 2), (2, 1, '0', 2), (3, 1, '0', 2);

-- Bus 3 (LMN456)
INSERT INTO asiento (numeroAsiento, piso, estado, idBus) VALUES
(1, 1, '0', 3), (2, 1, '0', 3), (3, 2, '0', 3);
