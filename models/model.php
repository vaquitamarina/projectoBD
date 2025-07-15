<?php
require_once("config/db.php");
class Modelo{
    private $db;
    
    public function __construct(){
        $this->db = new Conexion();
        $this->db = $this->db->conect();
    }
    //error de conversion de arrays a strings
    public function insert($tabla, $campos, $valores){
        $consulta = "INSERT INTO {$tabla} ({$campos}) VALUES ({$valores})";
        try {
            $stmt = $this->db->prepare($consulta);
            $resultado = $stmt->execute();
            return $resultado ? $this->db->lastInsertId() : false;
        } catch(PDOException $e) {
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