<?php
require_once __DIR__ . '/model.php';

class ModelUsuario extends Modelo {
    
    public function crearUsuario($username, $password, $email) {
        error_log("ModelUsuario::crearUsuario - Datos recibidos: username=$username, email=$email");
        
        if (empty($username) || empty($password) || empty($email)) {
            error_log("ERROR: Todos los campos son obligatorios para crear un usuario");
            return false;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error_log("ERROR: El formato del email no es válido");
            return false;
        }
        
        if (strlen($username) > 50) {
            error_log("ERROR: El username no puede tener más de 50 caracteres");
            return false;
        }
        
        if (strlen($email) > 100) {
            error_log("ERROR: El email no puede tener más de 100 caracteres");
            return false;
        }
        
        if (strlen($password) > 10) {
            error_log("ERROR: La contraseña no puede tener más de 10 caracteres (limitación del modelo original)");
            return false;
        }
        
        if (strlen($password) < 3) {
            error_log("ERROR: La contraseña debe tener al menos 3 caracteres");
            return false;
        }
        
        if ($this->existeEmail($email)) {
            error_log("ERROR: El email ya está registrado");
            return false;
        }
        
        if ($this->existeUsername($username)) {
            error_log("ERROR: El username ya está registrado");
            return false;
        }
        
        $campos = ['username', 'password', 'email'];
        $valores = [$username, $password, $email];
        
        error_log("ModelUsuario::crearUsuario - Insertando en BD con username=$username");
        
        $resultado = $this->insert('usuario', $campos, $valores);
        
        if ($resultado) {
            error_log("ModelUsuario::crearUsuario - Usuario creado exitosamente con ID: $resultado");
        } else {
            error_log("ModelUsuario::crearUsuario - Error al insertar en BD");
        }
        
        return $resultado;
    }
    
    public function autenticarUsuario($username, $password) {
        if (empty($username) || empty($password)) {
            error_log("ERROR: Username y contraseña son requeridos para autenticar");
            return false;
        }
        
        $consulta = "SELECT * FROM usuario WHERE username = ?";
        $resultado = $this->consultaPersonalizada($consulta, [$username]);
        
        if ($resultado && count($resultado) > 0) {
            $usuario = $resultado[0];
            
            if ($usuario['password'] === $password) {
                error_log("Autenticación exitosa para usuario: " . $usuario['username']);
                return $usuario;
            } else {
                error_log("Contraseña incorrecta para usuario: " . $username);
                return false;
            }
        } else {
            error_log("Usuario no encontrado: " . $username);
            return false;
        }
    }
    
    public function obtenerUsuarioPorId($idUsuario) {
        if (!is_numeric($idUsuario)) {
            return false;
        }
        
        return $this->getbyId('usuario', $idUsuario, 'idUsuario');
    }
    
    public function obtenerUsuarioPorUsername($username) {
        if (empty($username)) {
            return false;
        }
        
        $consulta = "SELECT * FROM usuario WHERE username = ?";
        $resultado = $this->consultaPersonalizada($consulta, [$username]);
        
        return $resultado ? $resultado[0] : false;
    }
    
    public function obtenerUsuarioPorEmail($email) {
        if (empty($email)) {
            return false;
        }
        
        $consulta = "SELECT * FROM usuario WHERE email = ?";
        $resultado = $this->consultaPersonalizada($consulta, [$email]);
        
        return $resultado ? $resultado[0] : false;
    }
    

    public function actualizarUsuario($idUsuario, $nombre = null, $apellido = null, $email = null, $password = null, $telefono = null) {
        if (!is_numeric($idUsuario)) {
            return false;
        }
        
        // Verificar que el usuario existe
        if (!$this->obtenerUsuarioPorId($idUsuario)) {
            error_log("ERROR: El usuario no existe");
            return false;
        }
        
        // Construir la consulta de actualización
        $campos = [];
        $valores = [];
        
        if ($nombre !== null) {
            $campos[] = "nombre = ?";
            $valores[] = $nombre;
        }
        
        if ($apellido !== null) {
            $campos[] = "apellido = ?";
            $valores[] = $apellido;
        }
        
        if ($email !== null) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                error_log("ERROR: El formato del email no es válido");
                return false;
            }
            
            // Verificar que el nuevo email no exista 
            if ($this->existeEmail($email, $idUsuario)) {
                error_log("ERROR: El email ya está en uso");
                return false;
            }
            
            $campos[] = "email = ?";
            $valores[] = $email;
        }
        
        if ($password !== null) {
            if (strlen($password) < 6) {
                error_log("ERROR: La contraseña debe tener al menos 6 caracteres");
                return false;
            }
            
            $campos[] = "password = ?";
            $valores[] = password_hash($password, PASSWORD_DEFAULT);
        }
        
        if ($telefono !== null) {
            $campos[] = "telefono = ?";
            $valores[] = $telefono;
        }
        
        if (empty($campos)) {
            error_log("ERROR: No hay campos para actualizar");
            return false;
        }
        
        $valores[] = $idUsuario;
        $consulta = "UPDATE usuario SET " . implode(", ", $campos) . " WHERE idUsuario = ?";
        
        $resultado = $this->consultaPersonalizada($consulta, $valores);
        return $resultado !== false;
    }
    
    public function eliminarUsuario($idUsuario) {
        if (!is_numeric($idUsuario)) {
            return false;
        }
        
        // Verificar si el usuario tiene tickets asociados
        $tickets = $this->consultaPersonalizada(
            "SELECT COUNT(*) as total FROM ticket WHERE idUsuario = ?", 
            [$idUsuario]
        );
        
        if ($tickets && $tickets[0]['total'] > 0) {
            error_log("ERROR: No se puede eliminar el usuario porque tiene tickets asociados");
            return false;
        }
        
        return $this->delete('usuario', "idUsuario = " . intval($idUsuario));
    }
    
    private function existeUsername($username, $excluirId = null) {
        $consulta = "SELECT COUNT(*) as total FROM usuario WHERE username = ?";
        $params = [$username];
        
        if ($excluirId !== null) {
            $consulta .= " AND idUsuario != ?";
            $params[] = $excluirId;
        }
        
        $resultado = $this->consultaPersonalizada($consulta, $params);
        return $resultado && $resultado[0]['total'] > 0;
    }
    

    private function existeEmail($email, $excluirId = null) {
        $consulta = "SELECT COUNT(*) as total FROM usuario WHERE email = ?";
        $params = [$email];
        
        if ($excluirId !== null) {
            $consulta .= " AND idUsuario != ?";
            $params[] = $excluirId;
        }
        
        $resultado = $this->consultaPersonalizada($consulta, $params);
        return $resultado && $resultado[0]['total'] > 0;
    }
    
    public function obtenerTodosLosUsuarios() {
        return $this->getAll('usuario');
    }
    
    public function obtenerTicketsUsuario($idUsuario) {
        if (!is_numeric($idUsuario)) {
            return [];
        }
        
        $consulta = "SELECT t.*, 
                        v.fecha, v.hInicio, v.hFinal,
                        a.numeroAsiento, a.piso,
                        p.nombres, p.apellidos
                    FROM ticket t
                    JOIN viaje v ON t.idViaje = v.idViaje
                    JOIN asiento a ON t.idAsiento = a.idAsiento
                    JOIN pasajero p ON t.idPasajero = p.idPasajero
                    WHERE t.idUsuario = ?
                    ORDER BY v.fecha DESC, v.hInicio DESC";
        
        return $this->consultaPersonalizada($consulta, [$idUsuario]);
    }
    
    public function comprarTicket($idUsuario, $idPasajero, $idViaje, $idAsiento) {
        if (!is_numeric($idUsuario) || !is_numeric($idPasajero) || !is_numeric($idViaje) || !is_numeric($idAsiento)) {
            error_log("ERROR: Los IDs deben ser numéricos");
            return false;
        }
        
        // Verificar que el usuario existe
        if (!$this->obtenerUsuarioPorId($idUsuario)) {
            error_log("ERROR: El usuario no existe");
            return false;
        }
        
        // Verificar que el asiento esté disponible
        $asientoDisponible = $this->consultaPersonalizada(
            "SELECT COUNT(*) as total FROM ticket WHERE idViaje = ? AND idAsiento = ?",
            [$idViaje, $idAsiento]
        );
        
        if ($asientoDisponible && $asientoDisponible[0]['total'] > 0) {
            error_log("ERROR: El asiento ya está ocupado");
            return false;
        }
        
        // Insertar el ticket
        $campos = ['idUsuario', 'idPasajero', 'idViaje', 'idAsiento'];
        $valores = [$idUsuario, $idPasajero, $idViaje, $idAsiento];
        
        return $this->insert('ticket', $campos, $valores);
    }

    public function obtenerEstadisticasUsuario($idUsuario) {
        if (!is_numeric($idUsuario)) {
            return false;
        }
        
        $consulta = "SELECT 
                        COUNT(*) as total_tickets,
                        MIN(v.fecha) as primer_viaje,
                        MAX(v.fecha) as ultimo_viaje
                    FROM ticket t
                    JOIN viaje v ON t.idViaje = v.idViaje
                    WHERE t.idUsuario = ?";
        
        $resultado = $this->consultaPersonalizada($consulta, [$idUsuario]);
        return $resultado ? $resultado[0] : false;
    }
}
?>
