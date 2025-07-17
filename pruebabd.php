<?php
echo "Iniciando script...<br>";

require_once("config/db.php");
echo "Archivo db.php incluido correctamente<br>";

try {
    $db = new Conexion();
    echo "Objeto Conexion creado<br>";
    
    $pdo = $db->conect();
    echo "Conexión establecida<br>";
    
    if ($pdo === null) {
        echo "Error: La conexión retornó null<br>";
        exit;
    }
    
    // Verificar si la tabla existe
    $checkTable = $pdo->query("SHOW TABLES LIKE 'bus'");
    if ($checkTable->rowCount() == 0) {
        echo "La tabla 'bus' no existe. Créala primero.<br>";
        exit;
    }
    echo "Tabla 'bus' encontrada<br>";
    
    // Insert de prueba
    $consulta = "INSERT INTO bus (placa, clase, estado, nAsientos) VALUES ('DEF456', 'Clase B', 'Activo', 35)";
    echo "Preparando consulta: " . $consulta . "<br>";
    
    $stmt = $pdo->prepare($consulta);
    $result = $stmt->execute();
    
    if ($result) {
        echo "✅ Registro insertado correctamente<br>";
        echo "ID del nuevo registro: " . $pdo->lastInsertId() . "<br>";
    } else {
        echo "❌ Error al insertar el registro<br>";
        print_r($stmt->errorInfo());
    }
    
} catch (Exception $e) {
    echo "Error capturado: " . $e->getMessage() . "<br>";
}

echo "Script terminado.";
?>
