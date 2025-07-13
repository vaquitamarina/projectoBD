<?php
// Configuración de la base de datos

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306'); 
define('DB_NAME', 'SistemaTransporte');
define('DB_USER', 'root');
define('DB_PASS', ''); 

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Conexión exitosa a la base de datos.";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>