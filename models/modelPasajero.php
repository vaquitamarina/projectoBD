<?php
require_once __DIR__ . '/model.php';

class ModelPasajero extends Modelo {

    public function crear($datos) {
        // Validar datos de entrada
        $errores = $this->validarDatos($datos);
        
        if (!empty($errores)) {
            error_log("ERROR en validación de pasajero: " . implode(', ', $errores));
            return false;
        }
        
        // Verificar unicidad del email
        if ($this->existeEmail($datos['email'])) {
            error_log("ERROR: El email ya existe");
            return false;
        }
        
        // Verificar unicidad del DNI si se proporciona
        if (!empty($datos['dni']) && $this->existeDni($datos['dni'])) {
            error_log("ERROR: El DNI ya existe");
            return false;
        }
        
        $campos = ['nombres', 'apellidos', 'email', 'fechaDeNacimiento', 'sexo', 'dni'];
        $valores = [
            $datos['nombres'],
            $datos['apellidos'],
            $datos['email'],
            $datos['fechaDeNacimiento'],
            $datos['sexo'],
            $datos['dni']
        ];
        
        return $this->insert('pasajero', $campos, $valores);
    }
    
    public function obtenerTodos() {
        return $this->getAll('pasajero');
    }
    
    public function obtenerPorId($idPasajero) {
        if (!is_numeric($idPasajero)) {
            return false;
        }
        
        return $this->getbyId('pasajero', $idPasajero, 'idPasajero');
    }
    
    public function obtenerPorEmail($email) {
        if (empty($email)) {
            return false;
        }
        
        $consulta = "SELECT * FROM pasajero WHERE email = ?";
        $resultado = $this->consultaPersonalizada($consulta, [$email]);
        
        return $resultado ? $resultado[0] : false;
    }

    public function obtenerPorDni($dni) {
        if (empty($dni)) {
            return false;
        }
        
        $consulta = "SELECT * FROM pasajero WHERE dni = ?";
        $resultado = $this->consultaPersonalizada($consulta, [$dni]);
        
        return $resultado ? $resultado[0] : false;
    }
    

    public function buscar($termino) {
        if (empty($termino)) {
            return [];
        }
        
        $consulta = "SELECT * FROM pasajero 
                    WHERE nombres LIKE ? 
                    OR apellidos LIKE ? 
                    OR dni LIKE ? 
                    OR email LIKE ?
                    ORDER BY nombres, apellidos";
        
        $parametro = "%{$termino}%";
        return $this->consultaPersonalizada($consulta, [$parametro, $parametro, $parametro, $parametro]);
    }
    
  
    public function actualizar($idPasajero, $datos) {
        if (!is_numeric($idPasajero)) {
            return false;
        }
        
        // Verificar que el pasajero existe
        if (!$this->obtenerPorId($idPasajero)) {
            error_log("ERROR: El pasajero no existe");
            return false;
        }
        
        // Validar datos
        $errores = $this->validarDatos($datos, $idPasajero);
        
        if (!empty($errores)) {
            error_log("ERROR en validación: " . implode(', ', $errores));
            return false;
        }
        
        // Verificar unicidad del email (excluyendo el registro actual)
        if (isset($datos['email']) && $this->existeEmail($datos['email'], $idPasajero)) {
            error_log("ERROR: El email ya existe");
            return false;
        }
        
        // Verificar unicidad del DNI (excluyendo el registro actual)
        if (isset($datos['dni']) && !empty($datos['dni']) && $this->existeDni($datos['dni'], $idPasajero)) {
            error_log("ERROR: El DNI ya existe");
            return false;
        }
        
        // Construir la consulta de actualización
        $campos = [];
        $valores = [];
        
        $camposPermitidos = ['nombres', 'apellidos', 'email', 'fechaDeNacimiento', 'sexo', 'dni'];
        
        foreach ($camposPermitidos as $campo) {
            if (isset($datos[$campo])) {
                $campos[] = "$campo = ?";
                $valores[] = $datos[$campo];
            }
        }
        
        if (empty($campos)) {
            error_log("ERROR: No hay campos para actualizar");
            return false;
        }
        
        $valores[] = $idPasajero;
        $consulta = "UPDATE pasajero SET " . implode(", ", $campos) . " WHERE idPasajero = ?";
        
        $resultado = $this->consultaPersonalizada($consulta, $valores);
        return $resultado !== false;
    }
   
    public function eliminar($idPasajero) {
        if (!is_numeric($idPasajero)) {
            return false;
        }
        
        // Verificar si el pasajero tiene tickets asociados
        $tickets = $this->consultaPersonalizada(
            "SELECT COUNT(*) as total FROM ticket WHERE idPasajero = ?", 
            [$idPasajero]
        );
        
        if ($tickets && $tickets[0]['total'] > 0) {
            error_log("ERROR: No se puede eliminar el pasajero porque tiene tickets asociados");
            return false;
        }
        
        return $this->delete('pasajero', "idPasajero = " . intval($idPasajero));
    }
    
    public function obtenerTickets($idPasajero) {
        if (!is_numeric($idPasajero)) {
            return [];
        }
        
        $consulta = "SELECT t.*, 
                        v.fecha, v.hInicio, v.hFinal,
                        a.numeroAsiento, a.piso,
                        b.placa AS placa_bus, 
                        b.clase AS clase_bus,
                        r.ciudadOrigen, 
                        r.ciudadFinal
                    FROM ticket t
                    JOIN viaje v ON t.idViaje = v.idViaje
                    JOIN asiento a ON t.idAsiento = a.idAsiento
                    JOIN viajebus vb ON v.idViaje = vb.idViaje
                    JOIN bus b ON vb.idBus = b.idBus
                    JOIN viajeruta vr ON v.idViaje = vr.idViaje
                    JOIN ruta r ON vr.idRuta = r.idRuta
                    WHERE t.idPasajero = ?
                    ORDER BY v.fecha DESC, v.hInicio DESC";
        
        return $this->consultaPersonalizada($consulta, [$idPasajero]);
    }
    
    public function obtenerEstadisticas($idPasajero) {
        if (!is_numeric($idPasajero)) {
            return false;
        }
        
        $consulta = "SELECT 
                        COUNT(*) as total_tickets,
                        MIN(v.fecha) as primer_viaje,
                        MAX(v.fecha) as ultimo_viaje,
                        COUNT(DISTINCT r.idRuta) as rutas_viajadas,
                        COUNT(DISTINCT b.clase) as clases_buses_usadas
                    FROM ticket t
                    JOIN viaje v ON t.idViaje = v.idViaje
                    JOIN viajebus vb ON v.idViaje = vb.idViaje
                    JOIN bus b ON vb.idBus = b.idBus
                    JOIN viajeruta vr ON v.idViaje = vr.idViaje
                    JOIN ruta r ON vr.idRuta = r.idRuta
                    WHERE t.idPasajero = ?";
        
        $resultado = $this->consultaPersonalizada($consulta, [$idPasajero]);
        return $resultado ? $resultado[0] : false;
    }
    
    private function validarDatos($datos, $excluirId = null) {
        $errores = [];
        
        // Validar campos requeridos
        if (empty($datos['nombres'])) {
            $errores[] = 'El nombre es requerido';
        }
        
        if (empty($datos['apellidos'])) {
            $errores[] = 'Los apellidos son requeridos';
        }
        
        if (empty($datos['email'])) {
            $errores[] = 'El email es requerido';
        } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El formato del email no es válido';
        }
        
        if (empty($datos['sexo'])) {
            $errores[] = 'El sexo es requerido';
        } elseif (!in_array($datos['sexo'], ['M', 'F'])) {
            $errores[] = 'El sexo debe ser M o F';
        }
        
        // Validar longitudes
        if (isset($datos['nombres']) && strlen($datos['nombres']) > 50) {
            $errores[] = 'El nombre no puede tener más de 50 caracteres';
        }
        
        if (isset($datos['apellidos']) && strlen($datos['apellidos']) > 100) {
            $errores[] = 'Los apellidos no pueden tener más de 100 caracteres';
        }
        
        if (isset($datos['email']) && strlen($datos['email']) > 100) {
            $errores[] = 'El email no puede tener más de 100 caracteres';
        }
        
        // Validar DNI si se proporciona
        if (!empty($datos['dni'])) {
            if (!preg_match('/^\d{8}$/', $datos['dni'])) {
                $errores[] = 'El DNI debe tener exactamente 8 dígitos';
            }
        }
        
        // Validar fecha de nacimiento si se proporciona
        if (!empty($datos['fechaDeNacimiento'])) {
            $fecha = DateTime::createFromFormat('Y-m-d', $datos['fechaDeNacimiento']);
            if (!$fecha || $fecha->format('Y-m-d') !== $datos['fechaDeNacimiento']) {
                $errores[] = 'La fecha de nacimiento debe tener el formato YYYY-MM-DD';
            }
        }
        
        return $errores;
    }
    
    private function existeEmail($email, $excluirId = null) {
        $consulta = "SELECT COUNT(*) as total FROM pasajero WHERE email = ?";
        $params = [$email];
        
        if ($excluirId !== null) {
            $consulta .= " AND idPasajero != ?";
            $params[] = $excluirId;
        }
        
        $resultado = $this->consultaPersonalizada($consulta, $params);
        return $resultado && $resultado[0]['total'] > 0;
    }
    
    private function existeDni($dni, $excluirId = null) {
        $consulta = "SELECT COUNT(*) as total FROM pasajero WHERE dni = ?";
        $params = [$dni];
        
        if ($excluirId !== null) {
            $consulta .= " AND idPasajero != ?";
            $params[] = $excluirId;
        }
        
        $resultado = $this->consultaPersonalizada($consulta, $params);
        return $resultado && $resultado[0]['total'] > 0;
    }
}
?>
