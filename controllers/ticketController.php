<?php
require_once 'models/modelTicket.php';

class TicketController {
    
    private $modelTicket;
    private $data = [];
    
    public function __construct() {
        $this->modelTicket = new TicketModel();
    }
    
    /**
     * Listar todos los tickets con información completa
     */
    public function index() {
        try {
            $tickets = $this->modelTicket->getAll('ticket');
            $this->data['titulo'] = 'Gestión de Tickets';
            $this->data['tickets'] = $tickets;
            $this->renderizar('ticketList');
        } catch (Exception $e) {
            $this->data['error'] = 'Error al cargar los tickets: ' . $e->getMessage();
            $this->renderizar('error');
        }
    }
    
    /**
     * Mostrar formulario para crear nuevo ticket
     */
    public function crear() {
        try {
            $this->data['titulo'] = 'Nuevo Ticket';
            $this->data['accion'] = 'crear';
            
            // Obtener datos necesarios para el formulario
            $this->data['pasajeros'] = $this->modelTicket->getAll('pasajero');
            $this->data['viajes'] = $this->modelTicket->getAll('viaje');
            $this->data['asientos'] = $this->modelTicket->getAll('asiento');
            $this->data['usuarios'] = $this->modelTicket->getAll('usuario');
            
            // Obtener boleteros de la tabla trabajador
            $this->data['boleteros'] = $this->modelTicket->consultaPersonalizada(
                "SELECT t.idTrabajador, t.nombres, t.apellidos, t.dni 
                FROM trabajador t 
                INNER JOIN boletero b ON t.idTrabajador = b.idTrabajador", 
                []
            );
            
            $this->renderizar('ticketForm');
        } catch (Exception $e) {
            $this->data['error'] = 'Error al cargar el formulario: ' . $e->getMessage();
            $this->renderizar('error');
        }
    }
    
    /**
     * Procesar creación de nuevo ticket
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('ticket');
            return;
        }
        
        try {
            $idPasajero = $_POST['idPasajero'] ?? null;
            $idViaje = $_POST['idViaje'] ?? null;
            $idAsiento = $_POST['idAsiento'] ?? null;
            $tipoVenta = $_POST['tipoVenta'] ?? null;
            $idBoletero = null;
            $idUsuario = null;
            
            // Validar datos básicos
            if (!$idPasajero || !$idViaje || !$idAsiento || !$tipoVenta) {
                throw new Exception("Todos los campos son obligatorios");
            }
            
            // Determinar tipo de venta
            if ($tipoVenta === 'boletero') {
                $idBoletero = $_POST['idBoletero'] ?? null;
                if (!$idBoletero) {
                    throw new Exception("Debe seleccionar un boletero");
                }
            } else if ($tipoVenta === 'usuario') {
                $idUsuario = $_POST['idUsuario'] ?? null;
                if (!$idUsuario) {
                    throw new Exception("Debe seleccionar un usuario");
                }
            } else {
                throw new Exception("Tipo de venta inválido");
            }
            
            // Crear el ticket
            $resultado = $this->modelTicket->crearTicket(
                $idPasajero, 
                $idViaje, 
                $idAsiento, 
                $idBoletero, 
                $idUsuario
            );
            
            if ($resultado) {
                $this->redirect('ticket', 'Ticket creado exitosamente');
            } else {
                throw new Exception("Error al crear el ticket");
            }
            
        } catch (Exception $e) {
            $this->data['error'] = $e->getMessage();
            $this->data['datos'] = $_POST;
            $this->crear();
        }
    }
    
    /**
     * Ver detalles de un ticket específico
     */
    public function ver($id) {
        if (!$id || !is_numeric($id)) {
            $this->redirect('ticket');
            return;
        }
        
        try {
            $ticket = $this->modelTicket->obtenerTicketCompleto($id);
            
            if (!$ticket) {
                $this->redirect('ticket', 'Ticket no encontrado');
                return;
            }
            
            $this->data['titulo'] = 'Detalles del Ticket';
            $this->data['ticket'] = $ticket;
            $this->renderizar('ticketDetail');
        } catch (Exception $e) {
            $this->data['error'] = 'Error al cargar el ticket: ' . $e->getMessage();
            $this->renderizar('error');
        }
    }
    
    /**
     * Mostrar formulario para editar ticket
     */
    public function editar($id) {
        if (!$id || !is_numeric($id)) {
            $this->redirect('ticket');
            return;
        }
        
        try {
            $ticket = $this->modelTicket->getbyId('ticket', $id, 'idTicket');
            
            if (!$ticket) {
                $this->redirect('ticket', 'Ticket no encontrado');
                return;
            }
            
            $this->data['titulo'] = 'Editar Ticket';
            $this->data['accion'] = 'editar';
            $this->data['ticket'] = $ticket;
            
            // Obtener datos necesarios para el formulario
            $this->data['pasajeros'] = $this->modelTicket->getAll('pasajero');
            $this->data['viajes'] = $this->modelTicket->getAll('viaje');
            $this->data['asientos'] = $this->modelTicket->getAll('asiento');
            $this->data['usuarios'] = $this->modelTicket->getAll('usuario');
            
            // Obtener boleteros
            $this->data['boleteros'] = $this->modelTicket->consultaPersonalizada(
                "SELECT t.idTrabajador, t.nombres, t.apellidos, t.dni 
                FROM trabajador t 
                INNER JOIN boletero b ON t.idTrabajador = b.idTrabajador", 
                []
            );
            
            $this->renderizar('ticketForm');
        } catch (Exception $e) {
            $this->data['error'] = 'Error al cargar el ticket: ' . $e->getMessage();
            $this->renderizar('error');
        }
    }
    
    /**
     * Procesar actualización de ticket
     */
    public function actualizar($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id || !is_numeric($id)) {
            $this->redirect('ticket');
            return;
        }
        
        try {
            $datos = [
                'idPasajero' => $_POST['idPasajero'] ?? null,
                'idViaje' => $_POST['idViaje'] ?? null,
                'idAsiento' => $_POST['idAsiento'] ?? null,
                'idBoletero' => $_POST['idBoletero'] ?? null,
                'idUsuario' => $_POST['idUsuario'] ?? null
            ];
            
            // Validar datos
            if (!$datos['idPasajero'] || !$datos['idViaje'] || !$datos['idAsiento']) {
                throw new Exception("Faltan datos obligatorios");
            }
            
            // Actualizar ticket
            $resultado = $this->modelTicket->update('ticket', $datos, "idTicket = {$id}");
            
            if ($resultado) {
                $this->redirect('ticket', 'Ticket actualizado exitosamente');
            } else {
                throw new Exception("Error al actualizar el ticket");
            }
            
        } catch (Exception $e) {
            $this->data['error'] = $e->getMessage();
            $this->editar($id);
        }
    }
    
    /**
     * Eliminar ticket
     */
    public function eliminar($id) {
        if (!$id || !is_numeric($id)) {
            $this->redirect('ticket');
            return;
        }
        
        try {
            $resultado = $this->modelTicket->eliminarTicket($id);
            
            if ($resultado) {
                $this->redirect('ticket', 'Ticket eliminado exitosamente');
            } else {
                $this->redirect('ticket', 'Error al eliminar el ticket');
            }
        } catch (Exception $e) {
            $this->redirect('ticket', 'Error al eliminar el ticket: ' . $e->getMessage());
        }
    }
    
    /**
     * Buscar tickets por diferentes criterios
     */
    public function buscar() {
        try {
            $criterio = $_GET['criterio'] ?? '';
            $valor = $_GET['valor'] ?? '';
            
            if (empty($criterio) || empty($valor)) {
                $this->index();
                return;
            }
            
            $tickets = [];
            
            switch ($criterio) {
                case 'pasajero':
                    $tickets = $this->modelTicket->consultaPersonalizada(
                        "SELECT t.* FROM ticket t 
                        INNER JOIN pasajero p ON t.idPasajero = p.idPasajero 
                        WHERE p.nombres LIKE ? OR p.apellidos LIKE ? OR p.dni LIKE ?",
                        ["%{$valor}%", "%{$valor}%", "%{$valor}%"]
                    );
                    break;
                    
                case 'viaje':
                    if (is_numeric($valor)) {
                        $tickets = $this->modelTicket->obtenerTicketsPorViaje($valor);
                    }
                    break;
                    
                case 'asiento':
                    if (is_numeric($valor)) {
                        $tickets = $this->modelTicket->get('ticket', "idAsiento = {$valor}");
                    }
                    break;
            }
            
            $this->data['titulo'] = 'Resultados de búsqueda';
            $this->data['tickets'] = $tickets;
            $this->data['criterio'] = $criterio;
            $this->data['valor'] = $valor;
            $this->renderizar('ticketList');
            
        } catch (Exception $e) {
            $this->data['error'] = 'Error en la búsqueda: ' . $e->getMessage();
            $this->renderizar('error');
        }
    }
    
    /**
     * Generar reporte de tickets
     */
    public function reporte() {
        try {
            $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
            $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
            
            $tickets = $this->modelTicket->consultaPersonalizada(
                "SELECT t.*, p.nombres, p.apellidos, v.fecha, v.hInicio, v.hFinal 
                FROM ticket t
                INNER JOIN pasajero p ON t.idPasajero = p.idPasajero
                INNER JOIN viaje v ON t.idViaje = v.idViaje
                WHERE v.fecha BETWEEN ? AND ?
                ORDER BY v.fecha DESC",
                [$fecha_inicio, $fecha_fin]
            );
            
            $this->data['titulo'] = 'Reporte de Tickets';
            $this->data['tickets'] = $tickets;
            $this->data['fecha_inicio'] = $fecha_inicio;
            $this->data['fecha_fin'] = $fecha_fin;
            $this->renderizar('ticketReport');
            
        } catch (Exception $e) {
            $this->data['error'] = 'Error al generar el reporte: ' . $e->getMessage();
            $this->renderizar('error');
        }
    }
    
    /**
     * Obtener asientos disponibles para un viaje específico (AJAX)
     */
    public function obtenerAsientosDisponibles() {
        if (!isset($_POST['idViaje']) || !is_numeric($_POST['idViaje'])) {
            echo json_encode(['error' => 'ID de viaje inválido']);
            return;
        }
        
        try {
            $idViaje = intval($_POST['idViaje']);
            
            // Obtener asientos disponibles
            $asientos = $this->modelTicket->consultaPersonalizada(
                "SELECT a.idAsiento, a.numeroAsiento, a.piso, a.estado,
                CASE WHEN t.idTicket IS NULL THEN 'Disponible' ELSE 'Ocupado' END AS disponibilidad
                FROM asiento a
                INNER JOIN viajebus vb ON a.idBus = vb.idBus
                LEFT JOIN ticket t ON a.idAsiento = t.idAsiento AND t.idViaje = ?
                WHERE vb.idViaje = ? AND a.estado = 'D'
                ORDER BY a.piso, a.numeroAsiento",
                [$idViaje, $idViaje]
            );
            
            // Filtrar solo disponibles
            $asientosDisponibles = array_filter($asientos, function($asiento) {
                return $asiento['disponibilidad'] === 'Disponible';
            });
            
            echo json_encode(['asientos' => array_values($asientosDisponibles)]);
            
        } catch (Exception $e) {
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
    
    /**
     * Renderizar vista con datos
     */
    private function renderizar($vista) {
        // Pasar datos a la vista
        extract($this->data);
        
        // Incluir la vista
        $archivo_vista = "views/{$vista}.php";
        if (file_exists($archivo_vista)) {
            include $archivo_vista;
        } else {
            echo "Vista no encontrada: {$vista}";
        }
    }
    
    /**
     * Redireccionar con mensaje opcional
     */
    private function redirect($controlador, $mensaje = null) {
        $url = "?controlador={$controlador}";
        if ($mensaje) {
            $url .= "&mensaje=" . urlencode($mensaje);
        }
        header("Location: {$url}");
        exit();
    }
}
?>
