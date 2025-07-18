<?php
require_once("../models/modelUsuario.php");

class UsuarioController {
    private $model;
    
    public function __construct() {
        $this->model = new ModelUsuario();
    }
    
    /**
     * Procesar login de usuario
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                $this->renderView('login', ['error' => 'Username y contraseña son requeridos']);
                return;
            }
            
            $usuario = $this->model->autenticarUsuario($username, $password);
            
            if ($usuario) {
                // Iniciar sesión
                session_start();
                $_SESSION['usuario_id'] = $usuario['idUsuario'];
                $_SESSION['usuario_username'] = $usuario['username'];
                $_SESSION['usuario_email'] = $usuario['email'];
                
                // Redireccionar al dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $this->renderView('login', ['error' => 'Credenciales incorrectas']);
            }
        } else {
            $this->renderView('login');
        }
    }
    
    /**
     * Procesar registro de usuario
     */
    public function registro() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $email = $_POST['email'] ?? '';
            $confirmarPassword = $_POST['confirmar_password'] ?? '';
            
            $errores = [];
            
            // Validaciones
            if (empty($username)) $errores[] = 'El username es requerido';
            if (empty($password)) $errores[] = 'La contraseña es requerida';
            if (empty($email)) $errores[] = 'El email es requerido';
            if ($password !== $confirmarPassword) $errores[] = 'Las contraseñas no coinciden';
            
            // Validar longitud según el modelo original
            if (strlen($username) > 50) $errores[] = 'El username no puede tener más de 50 caracteres';
            if (strlen($password) > 10) $errores[] = 'La contraseña no puede tener más de 10 caracteres';
            if (strlen($email) > 100) $errores[] = 'El email no puede tener más de 100 caracteres';
            
            if (empty($errores)) {
                $usuarioId = $this->model->crearUsuario($username, $password, $email);
                
                if ($usuarioId) {
                    // Redireccionar al login con mensaje de éxito
                    header("Location: login.php?registro=exitoso");
                    exit();
                } else {
                    $errores[] = 'Error al crear usuario. Puede que el username o email ya existan';
                }
            }
            
            $this->renderView('registro', ['errores' => $errores]);
        } else {
            $this->renderView('registro');
        }
    }
    
    /**
     * Mostrar dashboard del usuario
     */
    public function dashboard() {
        session_start();
        
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: login.php");
            exit();
        }
        
        $idUsuario = $_SESSION['usuario_id'];
        
        // Obtener información del usuario
        $usuario = $this->model->obtenerUsuarioPorId($idUsuario);
        
        // Obtener historial de tickets
        $tickets = $this->model->obtenerTicketsUsuario($idUsuario);
        
        // Obtener estadísticas
        $estadisticas = $this->model->obtenerEstadisticasUsuario($idUsuario);
        
        $data = [
            'usuario' => $usuario,
            'tickets' => $tickets,
            'estadisticas' => $estadisticas
        ];
        
        $this->renderView('dashboard', $data);
    }
    
    /**
     * Procesar compra de ticket
     */
    public function comprarTicket() {
        session_start();
        
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: login.php");
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idUsuario = $_SESSION['usuario_id'];
            $idPasajero = $_POST['idPasajero'] ?? '';
            $idViaje = $_POST['idViaje'] ?? '';
            $idAsiento = $_POST['idAsiento'] ?? '';
            
            $errores = [];
            
            // Validaciones
            if (empty($idPasajero)) $errores[] = 'Debe seleccionar un pasajero';
            if (empty($idViaje)) $errores[] = 'Debe seleccionar un viaje';
            if (empty($idAsiento)) $errores[] = 'Debe seleccionar un asiento';
            
            if (empty($errores)) {
                $ticketId = $this->model->comprarTicket($idUsuario, $idPasajero, $idViaje, $idAsiento);
                
                if ($ticketId) {
                    // Redireccionar con mensaje de éxito
                    header("Location: dashboard.php?compra=exitosa&ticket=$ticketId");
                    exit();
                } else {
                    $errores[] = 'Error al procesar la compra. Verifique que el asiento esté disponible';
                }
            }
            
            // Si hay errores, obtener datos necesarios para el formulario
            $viajes = $this->obtenerViajesDisponibles();
            $pasajeros = $this->obtenerPasajeros();
            
            $data = [
                'errores' => $errores,
                'viajes' => $viajes,
                'pasajeros' => $pasajeros
            ];
            
            $this->renderView('comprarTicket', $data);
        } else {
            // Obtener datos para el formulario
            $viajes = $this->obtenerViajesDisponibles();
            $pasajeros = $this->obtenerPasajeros();
            
            $data = [
                'viajes' => $viajes,
                'pasajeros' => $pasajeros
            ];
            
            $this->renderView('comprarTicket', $data);
        }
    }
    
    /**
     * Obtener asientos disponibles para un viaje (AJAX)
     */
    public function obtenerAsientosDisponibles() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['idViaje'])) {
            $idViaje = $_GET['idViaje'];
            
            $consulta = "SELECT a.idAsiento, a.numeroAsiento, a.piso, a.estado,
                        CASE WHEN t.idTicket IS NULL THEN 'Disponible' ELSE 'Ocupado' END AS disponibilidad
                        FROM asiento a
                        INNER JOIN viajebus vb ON a.idBus = vb.idBus
                        LEFT JOIN ticket t ON a.idAsiento = t.idAsiento AND t.idViaje = ?
                        WHERE vb.idViaje = ? AND a.estado = '0'
                        ORDER BY a.piso, a.numeroAsiento";
            
            $asientos = $this->model->consultaPersonalizada($consulta, [$idViaje, $idViaje]);
            
            // Filtrar solo disponibles
            $asientosDisponibles = array_filter($asientos, function($asiento) {
                return $asiento['disponibilidad'] === 'Disponible';
            });
            
            header('Content-Type: application/json');
            echo json_encode($asientosDisponibles);
            exit();
        }
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        session_start();
        session_destroy();
        header("Location: login.php");
        exit();
    }
    
    /**
     * Actualizar perfil del usuario
     */
    public function actualizarPerfil() {
        session_start();
        
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: login.php");
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idUsuario = $_SESSION['usuario_id'];
            $email = $_POST['email'] ?? null;
            $passwordActual = $_POST['password_actual'] ?? '';
            $passwordNuevo = $_POST['password_nuevo'] ?? '';
            $confirmarPassword = $_POST['confirmar_password'] ?? '';
            
            $errores = [];
            
            // Validar contraseña actual si se quiere cambiar
            if (!empty($passwordNuevo)) {
                if (empty($passwordActual)) {
                    $errores[] = 'Debe ingresar la contraseña actual';
                } else {
                    $usuario = $this->model->obtenerUsuarioPorId($idUsuario);
                    if ($usuario['password'] !== $passwordActual) {
                        $errores[] = 'La contraseña actual es incorrecta';
                    }
                }
                
                if ($passwordNuevo !== $confirmarPassword) {
                    $errores[] = 'Las contraseñas nuevas no coinciden';
                }
            }
            
            if (empty($errores)) {
                $actualizacion = $this->model->actualizarUsuario(
                    $idUsuario,
                    null, // No cambiar username
                    !empty($passwordNuevo) ? $passwordNuevo : null,
                    $email
                );
                
                if ($actualizacion) {
                    // Actualizar sesión si se cambió el email
                    if ($email) {
                        $_SESSION['usuario_email'] = $email;
                    }
                    
                    header("Location: dashboard.php?perfil=actualizado");
                    exit();
                } else {
                    $errores[] = 'Error al actualizar perfil';
                }
            }
            
            $usuario = $this->model->obtenerUsuarioPorId($idUsuario);
            $this->renderView('actualizarPerfil', ['errores' => $errores, 'usuario' => $usuario]);
        } else {
            $idUsuario = $_SESSION['usuario_id'];
            $usuario = $this->model->obtenerUsuarioPorId($idUsuario);
            $this->renderView('actualizarPerfil', ['usuario' => $usuario]);
        }
    }
    
    /**
     * Obtener viajes disponibles
     */
    private function obtenerViajesDisponibles() {
        $consulta = "SELECT v.*, r.ciudadOrigen, r.ciudadFinal, b.placa, b.clase
                    FROM viaje v
                    JOIN viajeruta vr ON v.idViaje = vr.idViaje
                    JOIN ruta r ON vr.idRuta = r.idRuta
                    JOIN viajebus vb ON v.idViaje = vb.idViaje
                    JOIN bus b ON vb.idBus = b.idBus
                    WHERE v.fecha >= CURDATE()
                    ORDER BY v.fecha, v.hInicio";
        
        return $this->model->consultaPersonalizada($consulta);
    }
    
    /**
     * Obtener pasajeros disponibles
     */
    private function obtenerPasajeros() {
        return $this->model->consultaPersonalizada("SELECT * FROM pasajero ORDER BY nombres, apellidos");
    }
    
    /**
     * Renderizar vista
     */
    private function renderView($viewName, $data = []) {
        extract($data);
        
        if (file_exists("../views/{$viewName}.php")) {
            require_once("../views/{$viewName}.php");
        } else {
            throw new Exception("Vista no encontrada: {$viewName}");
        }
    }
}

// Procesar la acción solicitada
if (isset($_GET['action'])) {
    $controller = new UsuarioController();
    $action = $_GET['action'];
    
    switch ($action) {
        case 'login':
            $controller->login();
            break;
        case 'registro':
            $controller->registro();
            break;
        case 'dashboard':
            $controller->dashboard();
            break;
        case 'comprarTicket':
            $controller->comprarTicket();
            break;
        case 'obtenerAsientosDisponibles':
            $controller->obtenerAsientosDisponibles();
            break;
        case 'logout':
            $controller->logout();
            break;
        case 'actualizarPerfil':
            $controller->actualizarPerfil();
            break;
        default:
            $controller->dashboard();
    }
}
?>
