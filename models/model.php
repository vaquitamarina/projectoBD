<?php
require_once("../config/db.php");
class Modelo{
    private $db;
    
    public function __construct(){
        $this->db = new Conexion();
        $this->db = $this->db->conect();
    }

    public function insert($tabla, $campos, $valores){
        if (!is_array($campos) || !is_array($valores)) {
            throw new Exception("Los campos y valores deben ser arrays.");
        }
        $campos = implode(", ", $campos);
        $placeholders = implode(", ", array_fill(0, count($valores), "?"));
        $consulta = "INSERT INTO {$tabla} ({$campos}) VALUES ({$placeholders})";
        try {
            $stmt = $this->db->prepare($consulta);
            $resultado = $stmt->execute($valores);
            if ($resultado) {
                return $this->db->lastInsertId();
            } else {
                error_log("Error en insert: " . print_r($stmt->errorInfo(), true));
                return false;
            }
        } catch(PDOException $e) {
            error_log("PDO Exception en insert: " . $e->getMessage());
            return false;
        }
    }
    
    public function getAll($tabla){
        $consulta = "SELECT * FROM {$tabla}";
        try {
            $resultado = $this->db->query($consulta);
            return $resultado->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // READ - Mostrar con condición
    public function get($tabla, $condicion = "1=1"){
        $consulta = "SELECT * FROM {$tabla} WHERE {$condicion}";
        try {
            $resultado = $this->db->query($consulta);
            return $resultado->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function getbyId($tabla, $id, $campoId = 'id'){
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
    
    public function update($tabla, $data, $condicion){
        $consulta = "UPDATE {$tabla} SET {$data} WHERE {$condicion}";
        try {
            $stmt = $this->db->prepare($consulta);
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function delete($tabla, $condicion){
        $consulta = "DELETE FROM {$tabla} WHERE {$condicion}";
        try {
            $stmt = $this->db->prepare($consulta);
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function consultaPersonalizada($consulta, $params = []){
        try {
            $stmt = $this->db->prepare($consulta);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }

    public function getTables(){
        $consulta = "SHOW TABLES";
        try {
            $resultado = $this->db->query($consulta);
            return $resultado->fetchAll(PDO::FETCH_COLUMN);
        } catch(PDOException $e) {
            return false;
        }
    }
}
?>