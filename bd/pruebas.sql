-- Active: 1752802333437@@127.0.0.1@3306@sistematransporte
USE SistemaTransporte;

-- Consulta de todos los viajes de Tacna a Moquegua
SELECT 
    v.idViaje,
    v.fecha,
    v.hInicio,
    v.hFinal,
    v.precio,
    r.idRuta,
    r.ciudadOrigen,
    r.ciudadFinal
FROM viaje v
JOIN viajeRuta vr ON v.idViaje = vr.idViaje
JOIN ruta r ON vr.idRuta = r.idRuta
WHERE r.ciudadOrigen = 'Tacna' 
  AND r.ciudadFinal = 'Moquegua'
ORDER BY v.fecha, v.hInicio;
