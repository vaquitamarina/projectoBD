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
