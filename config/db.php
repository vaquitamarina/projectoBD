<?php
// Cargar variables de entorno
require_once __DIR__ . '/env.php';

class Conexion {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;
    
    public function __construct() {
        // Configuración por defecto o desde variables de entorno
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->db_name = $_ENV['DB_NAME'] ?? 'SistemaTransporte';
        $this->username = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASS'] ?? '';
        $this->port = $_ENV['DB_PORT'] ?? '3308';
    }
    
    public function conect() {
        try {
            // Verificar si PDO está disponible
            if (!extension_loaded('pdo')) {
                throw new Exception("PDO no está habilitado en PHP");
            }
            
            if (!extension_loaded('pdo_mysql')) {
                throw new Exception("PDO MySQL driver no está habilitado. Verifica php.ini");
            }
            
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=utf8";
            
            $pdo = new PDO($dsn, $this->username, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            return $pdo;
        } catch(PDOException $e) {
            echo "Error de conexión PDO: " . $e->getMessage();
            return null;
        } catch(Exception $e) {
            echo "Error general: " . $e->getMessage();
            return null;
        }
    }
    
    // Método para probar la conexión
    public function testConnection() {
        $pdo = $this->conect();
        if ($pdo) {
            echo "✅ Conexión exitosa a la base de datos\n";
            return true;
        } else {
            echo "❌ Error al conectar a la base de datos\n";
            return false;
        }
    }
}
?>