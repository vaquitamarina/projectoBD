<?php
// API para manejar peticiones AJAX desde app.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/models/modelUsuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Permitir GET para obtener ciudades
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'obtener_ciudades') {
    // Manejar petición GET para obtener ciudades
    try {
        $model = new ModelUsuario();
        
        $consulta = "SELECT DISTINCT ciudadOrigen as ciudad FROM ruta 
                    UNION 
                    SELECT DISTINCT ciudadFinal as ciudad FROM ruta 
                    ORDER BY ciudad";
        $ciudades = $model->consultaPersonalizada($consulta);
        
        $response['success'] = true;
        $response['ciudades'] = $ciudades;
        
    } catch (Exception $e) {
        error_log('Error en API GET: ' . $e->getMessage());
        $response['message'] = 'Error interno del servidor';
    }
    
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$action = $_GET['action'] ?? '';
$response = ['success' => false, 'message' => ''];

try {
    $model = new ModelUsuario();
    
    switch ($action) {
        case 'login':
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                $response['message'] = 'Username y contraseña son requeridos';
                break;
            }
            
            $usuario = $model->autenticarUsuario($username, $password);
            
            if ($usuario) {
                session_start();
                $_SESSION['usuario_id'] = $usuario['idUsuario'];
                $_SESSION['usuario_username'] = $usuario['username'];
                $_SESSION['usuario_email'] = $usuario['email'];
                
                $response['success'] = true;
                $response['message'] = 'Login exitoso';
                $response['usuario'] = [
                    'id' => $usuario['idUsuario'],
                    'username' => $usuario['username'],
                    'email' => $usuario['email']
                ];
            } else {
                $response['message'] = 'Credenciales incorrectas';
            }
            break;
            
        case 'registro':
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
                $usuarioId = $model->crearUsuario($username, $password, $email);
                
                if ($usuarioId) {
                    $response['success'] = true;
                    $response['message'] = 'Usuario registrado exitosamente';
                    $response['usuario_id'] = $usuarioId;
                } else {
                    $response['message'] = 'Error al crear usuario. Puede que el username o email ya existan';
                }
            } else {
                $response['message'] = implode(', ', $errores);
            }
            break;
            
        case 'obtener_ciudades':
            $consulta = "SELECT DISTINCT ciudadOrigen as ciudad FROM ruta 
                        UNION 
                        SELECT DISTINCT ciudadFinal as ciudad FROM ruta 
                        ORDER BY ciudad";
            $ciudades = $model->consultaPersonalizada($consulta);
            
            $response['success'] = true;
            $response['ciudades'] = $ciudades;
            break;
            
        case 'buscar_viajes':
            $origen = $_POST['ciudadOrigen'] ?? '';
            $destino = $_POST['ciudadDestino'] ?? '';
            $fecha = $_POST['fechaViaje'] ?? '';
            
            if (empty($origen) || empty($destino) || empty($fecha)) {
                $response['message'] = 'Todos los campos son requeridos';
                break;
            }
            
            $consulta = "SELECT v.idViaje, v.fecha, v.hInicio, v.hFinal,
                            r.idRuta, r.ciudadOrigen, r.ciudadFinal, 
                            CASE 
                                WHEN b.clase = 'VIP' THEN 80
                                WHEN b.clase = 'Premium' THEN 60
                                WHEN b.clase = 'Económico' THEN 40
                                ELSE 50
                            END as precio,
                            b.idBus, b.placa, b.clase, b.nAsientos,
                            COUNT(DISTINCT a.idAsiento) as total_asientos,
                            COUNT(DISTINCT CASE WHEN t.idTicket IS NULL THEN a.idAsiento END) as asientos_disponibles
                        FROM viaje v
                        JOIN viajeruta vr ON v.idViaje = vr.idViaje
                        JOIN ruta r ON vr.idRuta = r.idRuta
                        JOIN viajebus vb ON v.idViaje = vb.idViaje
                        JOIN bus b ON vb.idBus = b.idBus AND b.estado = 'Disponible'
                        JOIN asiento a ON b.idBus = a.idBus AND a.estado = '0'
                        LEFT JOIN ticket t ON a.idAsiento = t.idAsiento AND t.idViaje = v.idViaje
                        WHERE r.ciudadOrigen = ? 
                        AND r.ciudadFinal = ? 
                        AND v.fecha = ?
                        GROUP BY v.idViaje, v.fecha, v.hInicio, v.hFinal, 
                                r.idRuta, r.ciudadOrigen, r.ciudadFinal,
                                b.idBus, b.placa, b.clase, b.nAsientos
                        HAVING asientos_disponibles > 0
                        ORDER BY v.hInicio";
            
            $viajes = $model->consultaPersonalizada($consulta, [$origen, $destino, $fecha]);
            
            $response['success'] = true;
            $response['viajes'] = $viajes;
            break;
            
        case 'obtener_asientos':
            $idViaje = $_POST['idViaje'] ?? '';
            
            if (empty($idViaje)) {
                $response['message'] = 'ID de viaje requerido';
                break;
            }
            
            $consulta = "SELECT a.idAsiento, a.numeroAsiento, a.piso, a.estado,
                            CASE WHEN t.idTicket IS NULL THEN 'disponible' ELSE 'ocupado' END as disponibilidad
                        FROM asiento a
                        JOIN viajebus vb ON a.idBus = vb.idBus
                        LEFT JOIN ticket t ON a.idAsiento = t.idAsiento AND t.idViaje = ?
                        WHERE vb.idViaje = ? AND a.estado = '0'
                        ORDER BY a.piso, a.numeroAsiento";
            
            $asientos = $model->consultaPersonalizada($consulta, [$idViaje, $idViaje]);
            
            $response['success'] = true;
            $response['asientos'] = $asientos;
            break;
            
        case 'procesar_compra':
            session_start();
            $idUsuario = $_SESSION['usuario_id'] ?? null;
            
            if (!$idUsuario) {
                $response['message'] = 'Debe iniciar sesión para realizar la compra';
                break;
            }
            
            $idViaje = $_POST['idViaje'] ?? '';
            $pasajeros = $_POST['pasajeros'] ?? [];
            $asientos = $_POST['asientos'] ?? [];
            
            if (empty($idViaje) || empty($pasajeros) || empty($asientos)) {
                $response['message'] = 'Datos incompletos para la compra';
                break;
            }
            
            try {
                $pdo = $model->getConnection();
                $pdo->beginTransaction();
                
                $tickets = [];
                
                // Procesar cada pasajero y su ticket
                for ($i = 0; $i < count($pasajeros); $i++) {
                    $pasajero = $pasajeros[$i];
                    $idAsiento = $asientos[$i];
                    
                    // Verificar si el pasajero ya existe por DNI
                    $consultaPasajero = "SELECT idPasajero FROM pasajero WHERE dni = ?";
                    $stmtPasajero = $pdo->prepare($consultaPasajero);
                    $stmtPasajero->execute([$pasajero['dni']]);
                    $pasajeroExistente = $stmtPasajero->fetch();
                    
                    if ($pasajeroExistente) {
                        $idPasajero = $pasajeroExistente['idPasajero'];
                    } else {
                        // Crear nuevo pasajero
                        $insertPasajero = "INSERT INTO pasajero (nombres, apellidos, email, fechaDeNacimiento, sexo, dni) 
                                          VALUES (?, ?, ?, ?, ?, ?)";
                        $stmtInsertPasajero = $pdo->prepare($insertPasajero);
                        $stmtInsertPasajero->execute([
                            $pasajero['nombres'],
                            $pasajero['apellidos'],
                            $pasajero['email'],
                            $pasajero['fechaNacimiento'],
                            $pasajero['sexo'],
                            $pasajero['dni']
                        ]);
                        $idPasajero = $pdo->lastInsertId();
                    }
                    
                    // Verificar que el asiento esté disponible
                    $consultaAsiento = "SELECT COUNT(*) as ocupado FROM ticket WHERE idAsiento = ? AND idViaje = ?";
                    $stmtAsiento = $pdo->prepare($consultaAsiento);
                    $stmtAsiento->execute([$idAsiento, $idViaje]);
                    $asientoOcupado = $stmtAsiento->fetch();
                    
                    if ($asientoOcupado['ocupado'] > 0) {
                        throw new Exception("El asiento ya está ocupado");
                    }
                    
                    // Crear ticket
                    $insertTicket = "INSERT INTO ticket (idPasajero, idViaje, idAsiento, idUsuario) 
                                    VALUES (?, ?, ?, ?)";
                    $stmtInsertTicket = $pdo->prepare($insertTicket);
                    $stmtInsertTicket->execute([$idPasajero, $idViaje, $idAsiento, $idUsuario]);
                    $idTicket = $pdo->lastInsertId();
                    
                    $tickets[] = [
                        'idTicket' => $idTicket,
                        'idPasajero' => $idPasajero,
                        'nombres' => $pasajero['nombres'],
                        'apellidos' => $pasajero['apellidos']
                    ];
                }
                
                $pdo->commit();
                
                $response['success'] = true;
                $response['message'] = 'Compra realizada exitosamente';
                $response['tickets'] = $tickets;
                $response['codigoReserva'] = 'TR' . time();
                
            } catch (Exception $e) {
                $pdo->rollBack();
                error_log('Error en compra: ' . $e->getMessage());
                $response['message'] = 'Error al procesar la compra: ' . $e->getMessage();
            }
            break;
            
        default:
            $response['message'] = 'Acción no válida';
            break;
    }
    
} catch (Exception $e) {
    error_log('Error en API: ' . $e->getMessage());
    $response['message'] = 'Error interno del servidor';
}

echo json_encode($response);
?>
