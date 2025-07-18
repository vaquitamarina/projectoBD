<?php
require_once __DIR__ . "/../models/modelUsuario.php";
require_once __DIR__ . "/../models/modelTicket.php";
require_once __DIR__ . "/../models/modelPasajero.php";

class AppController {
    private $modelUsuario;
    private $modelTicket;
    private $modelPasajero;
    private $data;
    
    public function __construct() {
        $this->modelUsuario = new ModelUsuario();
        $this->modelTicket = new TicketModel();
        $this->modelPasajero = new ModelPasajero();
        $this->data = [];
    }
    
    /**
     * Página principal de compra de tickets
     */
    public function comprarTickets() {
        session_start();
        
        // Verificar si hay usuario logueado
        if (!isset($_SESSION['usuario_id'])) {
            $this->renderView('loginRequerido');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarCompra();
        } else {
            $this->mostrarFormularioCompra();
        }
    }
    
    /**
     * Mostrar formulario de compra
     */
    private function mostrarFormularioCompra() {
        $idUsuario = $_SESSION['usuario_id'];
        
        // Obtener datos necesarios para el formulario
        $viajes = $this->obtenerViajesDisponibles();
        $pasajeros = $this->modelPasajero->obtenerTodos();
        
        $data = [
            'usuario' => $this->modelUsuario->obtenerUsuarioPorId($idUsuario),
            'viajes' => $viajes,
            'pasajeros' => $pasajeros,
            'titulo' => 'Comprar Tickets'
        ];
        
        $this->renderView('comprarTickets', $data);
    }
    
    /**
     * Procesar compra de ticket
     */
    private function procesarCompra() {
        $idUsuario = $_SESSION['usuario_id'];
        $idPasajero = $_POST['idPasajero'] ?? '';
        $idViaje = $_POST['idViaje'] ?? '';
        $idAsiento = $_POST['idAsiento'] ?? '';
        $crearNuevoPasajero = $_POST['crear_pasajero'] ?? false;
        
        $errores = [];
        
        try {
            // Si se seleccionó crear nuevo pasajero
            if ($crearNuevoPasajero) {
                $idPasajero = $this->crearNuevoPasajero($_POST);
                if (!$idPasajero) {
                    $errores[] = 'Error al crear el pasajero';
                }
            }
            
            // Validaciones
            if (empty($idPasajero)) $errores[] = 'Debe seleccionar o crear un pasajero';
            if (empty($idViaje)) $errores[] = 'Debe seleccionar un viaje';
            if (empty($idAsiento)) $errores[] = 'Debe seleccionar un asiento';
            
            if (empty($errores)) {
                // Realizar compra usando el ModelUsuario
                $ticketId = $this->modelUsuario->comprarTicket($idUsuario, $idPasajero, $idViaje, $idAsiento);
                
                if ($ticketId) {
                    // Redireccionar con mensaje de éxito
                    $this->redirect('confirmacion', ['ticket' => $ticketId]);
                    return;
                } else {
                    $errores[] = 'Error al procesar la compra. Verifique que el asiento esté disponible';
                }
            }
            
            // Si hay errores, mostrar formulario con errores
            $this->mostrarFormularioCompra();
            $this->data['errores'] = $errores;
            
        } catch (Exception $e) {
            $errores[] = 'Error interno: ' . $e->getMessage();
            $this->mostrarFormularioCompra();
            $this->data['errores'] = $errores;
        }
    }
    
    /**
     * Crear nuevo pasajero desde el formulario
     */
    private function crearNuevoPasajero($datos) {
        $pasajeroData = [
            'nombres' => $datos['pasajero_nombres'] ?? '',
            'apellidos' => $datos['pasajero_apellidos'] ?? '',
            'email' => $datos['pasajero_email'] ?? '',
            'fechaDeNacimiento' => $datos['pasajero_fecha_nacimiento'] ?? null,
            'sexo' => $datos['pasajero_sexo'] ?? '',
            'dni' => $datos['pasajero_dni'] ?? ''
        ];
        
        return $this->modelPasajero->crear($pasajeroData);
    }
    
    /**
     * Mostrar confirmación de compra
     */
    public function confirmacion() {
        session_start();
        
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('login');
            return;
        }
        
        $ticketId = $_GET['ticket'] ?? null;
        
        if (!$ticketId) {
            $this->redirect('comprarTickets');
            return;
        }
        
        // Obtener detalles del ticket
        $ticket = $this->modelTicket->obtenerTicketCompleto($ticketId);
        
        if (!$ticket) {
            $this->redirect('comprarTickets', ['error' => 'Ticket no encontrado']);
            return;
        }
        
        $data = [
            'ticket' => $ticket,
            'titulo' => 'Confirmación de Compra'
        ];
        
        $this->renderView('confirmacion', $data);
    }
    
    /**
     * Dashboard del usuario
     */
    public function dashboard() {
        session_start();
        
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('login');
            return;
        }
        
        $idUsuario = $_SESSION['usuario_id'];
        
        // Obtener información del usuario
        $usuario = $this->modelUsuario->obtenerUsuarioPorId($idUsuario);
        
        // Obtener historial de tickets
        $tickets = $this->modelUsuario->obtenerTicketsUsuario($idUsuario);
        
        // Obtener estadísticas
        $estadisticas = $this->modelUsuario->obtenerEstadisticasUsuario($idUsuario);
        
        $data = [
            'usuario' => $usuario,
            'tickets' => $tickets,
            'estadisticas' => $estadisticas,
            'titulo' => 'Mi Dashboard'
        ];
        
        $this->renderView('dashboard', $data);
    }
    
    /**
     * API para obtener asientos disponibles (AJAX)
     */
    public function asientosDisponibles() {
        if (!isset($_GET['idViaje']) || !is_numeric($_GET['idViaje'])) {
            $this->jsonResponse(['error' => 'ID de viaje inválido']);
            return;
        }
        
        $idViaje = $_GET['idViaje'];
        
        $consulta = "SELECT a.idAsiento, a.numeroAsiento, a.piso, a.estado,
                    CASE WHEN t.idTicket IS NULL THEN 'Disponible' ELSE 'Ocupado' END AS disponibilidad
                    FROM asiento a
                    INNER JOIN viajebus vb ON a.idBus = vb.idBus
                    LEFT JOIN ticket t ON a.idAsiento = t.idAsiento AND t.idViaje = ?
                    WHERE vb.idViaje = ? AND a.estado = '0'
                    ORDER BY a.piso, a.numeroAsiento";
        
        $asientos = $this->modelTicket->consultaPersonalizada($consulta, [$idViaje, $idViaje]);
        
        // Filtrar solo disponibles
        $asientosDisponibles = array_filter($asientos, function($asiento) {
            return $asiento['disponibilidad'] === 'Disponible';
        });
        
        $this->jsonResponse(['asientos' => array_values($asientosDisponibles)]);
    }
    
    /**
     * API para obtener información del viaje (AJAX)
     */
    public function infoViaje() {
        if (!isset($_GET['idViaje']) || !is_numeric($_GET['idViaje'])) {
            $this->jsonResponse(['error' => 'ID de viaje inválido']);
            return;
        }
        
        $idViaje = $_GET['idViaje'];
        
        $consulta = "SELECT v.*, r.ciudadOrigen, r.ciudadFinal, b.placa, b.clase, b.nAsientos
                    FROM viaje v
                    JOIN viajeruta vr ON v.idViaje = vr.idViaje
                    JOIN ruta r ON vr.idRuta = r.idRuta
                    JOIN viajebus vb ON v.idViaje = vb.idViaje
                    JOIN bus b ON vb.idBus = b.idBus
                    WHERE v.idViaje = ?";
        
        $viaje = $this->modelTicket->consultaPersonalizada($consulta, [$idViaje]);
        
        if ($viaje) {
            $this->jsonResponse(['viaje' => $viaje[0]]);
        } else {
            $this->jsonResponse(['error' => 'Viaje no encontrado']);
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
        
        return $this->modelTicket->consultaPersonalizada($consulta);
    }
    
    /**
     * Renderizar vista
     */
    private function renderView($viewName, $data = []) {
        $this->data = $data;
        extract($data);
        
        $viewPath = __DIR__ . "/../views/{$viewName}.php";
        if (file_exists($viewPath)) {
            require_once($viewPath);
        } else {
            throw new Exception("Vista no encontrada: {$viewName}");
        }
    }
    
    /**
     * Redireccionar
     */
    private function redirect($action, $params = []) {
        $url = "?action={$action}";
        if (!empty($params)) {
            $url .= '&' . http_build_query($params);
        }
        header("Location: {$url}");
        exit();
    }
    
    /**
     * Respuesta JSON
     */
    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}

// Manejo de rutas
if (isset($_GET['action'])) {
    $controller = new AppController();
    $action = $_GET['action'];
    
    switch ($action) {
        case 'comprarTickets':
            $controller->comprarTickets();
            break;
        case 'confirmacion':
            $controller->confirmacion();
            break;
        case 'dashboard':
            $controller->dashboard();
            break;
        case 'asientosDisponibles':
            $controller->asientosDisponibles();
            break;
        case 'infoViaje':
            $controller->infoViaje();
            break;
        default:
            $controller->comprarTickets();
    }
} else {
    $controller = new AppController();
    $controller->comprarTickets();
}
?>