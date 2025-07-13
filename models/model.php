<?php
class Modelo{
    private $db;
    private $registros;
    
    public function __construct(){
        $this->registros = array();
        try {
            $this->db = new PDO('mysql:host=localhost;dbname=sistematransporte;charset=utf8', "root", "");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
    
    // CREATE - Insertar con campos específicos
    public function insertar($tabla, $campos, $valores){
        $consulta = "INSERT INTO {$tabla} ({$campos}) VALUES ({$valores})";
        try {
            $stmt = $this->db->prepare($consulta);
            $resultado = $stmt->execute();
            return $resultado ? $this->db->lastInsertId() : false;
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // READ - Mostrar todos los registros
    public function mostrarTodos($tabla){
        $consulta = "SELECT * FROM {$tabla}";
        try {
            $resultado = $this->db->query($consulta);
            return $resultado->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // READ - Mostrar con condición
    public function mostrar($tabla, $condicion = "1=1"){
        $consulta = "SELECT * FROM {$tabla} WHERE {$condicion}";
        try {
            $resultado = $this->db->query($consulta);
            return $resultado->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // READ - Mostrar un registro por ID
    public function mostrarPorId($tabla, $id, $campoId = 'id'){
        $consulta = "SELECT * FROM {$tabla} WHERE {$campoId} = :id";
        try {
            $stmt = $this->db->prepare($consulta);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // UPDATE - Actualizar
    public function actualizar($tabla, $data, $condicion){
        $consulta = "UPDATE {$tabla} SET {$data} WHERE {$condicion}";
        try {
            $stmt = $this->db->prepare($consulta);
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // DELETE - Eliminar
    public function eliminar($tabla, $condicion){
        $consulta = "DELETE FROM {$tabla} WHERE {$condicion}";
        try {
            $stmt = $this->db->prepare($consulta);
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Método para consultas personalizadas
    public function consultaPersonalizada($consulta, $params = []){
        try {
            $stmt = $this->db->prepare($consulta);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }
}
?>