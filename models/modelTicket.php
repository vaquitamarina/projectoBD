<?php
require_once 'model.php';

class TicketModel extends Modelo {
    
    public function crearTicket($idPasajero, $idViaje, $idAsiento, $idBoletero = null, $idUsuario = null) {
        // Validar tipos de datos
        if (!is_numeric($idPasajero) || !is_numeric($idViaje) || !is_numeric($idAsiento)) {
            error_log("ERROR: Los IDs deben ser números válidos");
            return false;
        }
        
        // Validar que solo uno de los dos esté presente
        if(($idBoletero !== null && $idUsuario !== null) || ($idBoletero === null && $idUsuario === null)) {
            error_log("ERROR: Se debe especificar exactamente uno entre idBoletero o idUsuario");
            return false;
        }
        
        // Verificar disponibilidad del asiento
        if (!$this->verificarAsientoDisponible($idViaje, $idAsiento)) {
            error_log("ERROR: El asiento ya está ocupado para este viaje");
            return false;
        }

        $campos = ['idPasajero', 'idViaje', 'idAsiento', 'idBoletero', 'idUsuario'];
        $valores = [$idPasajero, $idViaje, $idAsiento, $idBoletero, $idUsuario];
        
        return $this->insert('ticket', $campos, $valores);
    }
    
    public function obtenerTicketCompleto($idTicket) {
        if (!is_numeric($idTicket)) {
            return false;
        }
        
        $consulta = "SELECT t.*, 
                        p.nombres AS pasajero_nombres, p.apellidos AS pasajero_apellidos, 
                        v.fecha, v.hInicio, v.hFinal,
                        a.numeroAsiento, a.piso,
                        b.placa AS placa_bus, b.clase AS clase_bus,
                        r.ciudadOrigen, r.ciudadFinal
                    FROM ticket t
                    JOIN pasajero p ON t.idPasajero = p.idPasajero
                    JOIN viaje v ON t.idViaje = v.idViaje
                    JOIN asiento a ON t.idAsiento = a.idAsiento
                    JOIN viajebus vb ON v.idViaje = vb.idViaje
                    JOIN bus b ON vb.idBus = b.idBus
                    JOIN viajeruta vr ON v.idViaje = vr.idViaje
                    JOIN ruta r ON vr.idRuta = r.idRuta
                    WHERE t.idTicket = ?";
        
        $resultado = $this->consultaPersonalizada($consulta, [$idTicket]);
        return $resultado ? $resultado[0] : false;
    }
    
    public function obtenerTicketsPorPasajero($idPasajero) {
        return $this->get('ticket', "idPasajero = " . intval($idPasajero));
    }
    
    public function eliminarTicket($idTicket) {
        return $this->delete('ticket', "idTicket = " . intval($idTicket));
    }
    
    public function obtenerTicketsPorViaje($idViaje) {
        return $this->get('ticket', "idViaje = " . intval($idViaje));
    }
    
    public function verificarAsientoDisponible($idViaje, $idAsiento) {
        $tickets = $this->get('ticket', "idViaje = " . intval($idViaje) . " AND idAsiento = " . intval($idAsiento));
        return empty($tickets);
    }
}
?>