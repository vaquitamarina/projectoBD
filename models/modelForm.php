<?php
require_once( __DIR__ . "/../config/db.php");

class ModelForm{
    private $db;
    private $tableSchemas;
    
    public function __construct(){
        $this->db = new Conexion();
        $this->db = $this->db->conect();
        $this->initializeTableSchemas();
    }
    
    private function initializeTableSchemas(){
        $this->tableSchemas = [
            'usuario' => [
                'idUsuario' => ['type' => 'int', 'auto_increment' => true, 'primary_key' => true],
                'username' => ['type' => 'string', 'max_length' => 50, 'required' => true, 'unique' => true],
                'password' => ['type' => 'string', 'max_length' => 10, 'required' => true],
                'email' => ['type' => 'email', 'max_length' => 100, 'required' => true, 'unique' => true]
            ],
            'pasajero' => [
                'idPasajero' => ['type' => 'int', 'auto_increment' => true, 'primary_key' => true],
                'nombres' => ['type' => 'string', 'max_length' => 50, 'required' => true],
                'apellidos' => ['type' => 'string', 'max_length' => 100, 'required' => true],
                'email' => ['type' => 'email', 'max_length' => 100, 'required' => true],
                'fechaDeNacimiento' => ['type' => 'date', 'required' => false],
                'sexo' => ['type' => 'char', 'max_length' => 1, 'required' => true, 'allowed_values' => ['M', 'F']],
                'dni' => ['type' => 'string', 'max_length' => 8, 'required' => false, 'unique' => true, 'pattern' => '/^\d{8}$/']
            ],
            'bus' => [
                'idBus' => ['type' => 'int', 'auto_increment' => true, 'primary_key' => true],
                'placa' => ['type' => 'string', 'max_length' => 10, 'required' => true, 'unique' => true],
                'clase' => ['type' => 'string', 'max_length' => 20, 'required' => true],
                'estado' => ['type' => 'string', 'max_length' => 20, 'required' => true, 'allowed_values' => ['Activo', 'Inactivo', 'Mantenimiento']],
                'nAsientos' => ['type' => 'int', 'required' => true, 'min_value' => 1, 'max_value' => 100]
            ],
            'trabajador' => [
                'idTrabajador' => ['type' => 'int', 'auto_increment' => true, 'primary_key' => true],
                'nombres' => ['type' => 'string', 'max_length' => 50, 'required' => true],
                'apellidos' => ['type' => 'string', 'max_length' => 50, 'required' => true],
                'sexo' => ['type' => 'char', 'max_length' => 1, 'required' => true, 'allowed_values' => ['M', 'F']],
                'dni' => ['type' => 'string', 'max_length' => 8, 'required' => false, 'unique' => true, 'pattern' => '/^\d{8}$/']
            ],
            'viaje' => [
                'idViaje' => ['type' => 'int', 'auto_increment' => true, 'primary_key' => true],
                'fecha' => ['type' => 'date', 'required' => true],
                'hInicio' => ['type' => 'time', 'required' => true],
                'hFinal' => ['type' => 'time', 'required' => true],
                'precio' => ['type' => 'int', 'required' => true]
            ],
            'asiento' => [
                'idAsiento' => ['type' => 'int', 'auto_increment' => true, 'primary_key' => true],
                'numeroAsiento' => ['type' => 'int', 'required' => true, 'min_value' => 1],
                'piso' => ['type' => 'int', 'required' => true, 'allowed_values' => [1, 2]],
                'estado' => ['type' => 'char', 'max_length' => 1, 'required' => true, 'allowed_values' => ['D', 'O']], // D=Disponible, O=Ocupado
                'idBus' => ['type' => 'int', 'required' => true, 'foreign_key' => 'bus.idBus']
            ],
            'ruta' => [
                'idRuta' => ['type' => 'int', 'auto_increment' => true, 'primary_key' => true],
                'ciudadOrigen' => ['type' => 'string', 'max_length' => 50, 'required' => true],
                'ciudadFinal' => ['type' => 'string', 'max_length' => 50, 'required' => true]
            ],
            'ticket' => [
                'idTicket' => ['type' => 'int', 'auto_increment' => true, 'primary_key' => true],
                'idPasajero' => ['type' => 'int', 'required' => true, 'foreign_key' => 'pasajero.idPasajero'],
                'idViaje' => ['type' => 'int', 'required' => true, 'foreign_key' => 'viaje.idViaje'],
                'idAsiento' => ['type' => 'int', 'required' => true, 'foreign_key' => 'asiento.idAsiento'],
                'idBoletero' => ['type' => 'int', 'required' => false, 'foreign_key' => 'boletero.idTrabajador'],
                'idUsuario' => ['type' => 'int', 'required' => false, 'foreign_key' => 'usuario.idUsuario']
            ],
            'boletero' => [
                'idTrabajador' => ['type' => 'int', 'required' => true, 'primary_key' => true, 'foreign_key' => 'trabajador.idTrabajador']
            ],
            'viajeruta' => [
                'idViaje' => ['type' => 'int', 'required' => true, 'primary_key' => true, 'foreign_key' => 'viaje.idViaje'],
                'idRuta' => ['type' => 'int', 'required' => true, 'primary_key' => true, 'foreign_key' => 'ruta.idRuta']
            ],
            'viajebus' => [
                'idViaje' => ['type' => 'int', 'required' => true, 'primary_key' => true, 'foreign_key' => 'viaje.idViaje'],
                'idBus' => ['type' => 'int', 'required' => true, 'primary_key' => true, 'foreign_key' => 'bus.idBus']
            ],
            'trabajadorbus' => [
                'idTrabajador' => ['type' => 'int', 'required' => true, 'primary_key' => true, 'foreign_key' => 'trabajador.idTrabajador'],
                'idBus' => ['type' => 'int', 'required' => true, 'foreign_key' => 'bus.idBus']
            ],
            'chofer' => [
                'idTrabajador' => ['type' => 'int', 'required' => true, 'primary_key' => true, 'foreign_key' => 'trabajador.idTrabajador']
            ]
        ];
    }
    
    private function validateData($tableName, $data, $isUpdate = false){
        if (!isset($this->tableSchemas[$tableName])) {
            throw new Exception("Tabla '$tableName' no está configurada en el esquema");
        }
        
        $schema = $this->tableSchemas[$tableName];
        $errors = [];
        
        foreach ($schema as $field => $rules) {
            // Saltar campos auto_increment en inserción
            if (!$isUpdate && isset($rules['auto_increment']) && $rules['auto_increment']) {
                continue;
            }
            
            $value = $data[$field] ?? null;
            
            // Validar campos requeridos
            if (isset($rules['required']) && $rules['required'] && (empty($value) && $value !== '0')) {
                $errors[$field] = "El campo '$field' es requerido";
                continue;
            }
            
            // Si el valor está vacío y no es requerido, continuar
            if (empty($value) && $value !== '0') {
                continue;
            }
            
            // Validar tipo de dato
            switch ($rules['type']) {
                case 'int':
                    if (!is_numeric($value) || (int)$value != $value) {
                        $errors[$field] = "El campo '$field' debe ser un número entero";
                    } else {
                        $intValue = (int)$value;
                        if (isset($rules['min_value']) && $intValue < $rules['min_value']) {
                            $errors[$field] = "El campo '$field' debe ser mayor o igual a {$rules['min_value']}";
                        }
                        if (isset($rules['max_value']) && $intValue > $rules['max_value']) {
                            $errors[$field] = "El campo '$field' debe ser menor o igual a {$rules['max_value']}";
                        }
                    }
                    break;
                    
                case 'string':
                case 'char':
                    if (isset($rules['max_length']) && strlen($value) > $rules['max_length']) {
                        $errors[$field] = "El campo '$field' no puede tener más de {$rules['max_length']} caracteres";
                    }
                    if (isset($rules['pattern']) && !preg_match($rules['pattern'], $value)) {
                        $errors[$field] = "El campo '$field' no tiene el formato correcto";
                    }
                    break;
                    
                case 'email':
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[$field] = "El campo '$field' debe ser un email válido";
                    }
                    break;
                    
                case 'date':
                    if (!$this->validateDate($value)) {
                        $errors[$field] = "El campo '$field' debe ser una fecha válida (YYYY-MM-DD)";
                    }
                    break;
                    
                case 'time':
                    if (!$this->validateTime($value)) {
                        $errors[$field] = "El campo '$field' debe ser una hora válida (HH:MM:SS)";
                    }
                    break;
            }
            
            // Validar valores permitidos
            if (isset($rules['allowed_values']) && !in_array($value, $rules['allowed_values'])) {
                $allowedStr = implode(', ', $rules['allowed_values']);
                $errors[$field] = "El campo '$field' debe ser uno de los siguientes valores: $allowedStr";
            }
        }
        
        return $errors;
    }
    
    private function validateDate($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
    
    private function validateTime($time) {
        $t = DateTime::createFromFormat('H:i:s', $time);
        if (!$t) {
            $t = DateTime::createFromFormat('H:i', $time);
            return $t && $t->format('H:i') === $time;
        }
        return $t && $t->format('H:i:s') === $time;
    }
    
    private function checkUnique($tableName, $field, $value, $excludeId = null) {
        $schema = $this->tableSchemas[$tableName];
        if (!isset($schema[$field]['unique']) || !$schema[$field]['unique']) {
            return true; 
        }
        
        $sql = "SELECT COUNT(*) FROM $tableName WHERE $field = ?";
        $params = [$value];
        
        if ($excludeId !== null) {
            $primaryKey = $this->getPrimaryKey($tableName);
            $sql .= " AND $primaryKey != ?";
            $params[] = $excludeId;
        }
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn() == 0;
        } catch (PDOException $e) {
            error_log("Error checking unique constraint: " . $e->getMessage());
            return false;
        }
    }
    
    public function getPrimaryKey($tableName) {
        $schema = $this->tableSchemas[$tableName];
        foreach ($schema as $field => $rules) {
            if (isset($rules['primary_key']) && $rules['primary_key']) {
                return $field;
            }
        }
        return 'id'; 
    }

    public function insert($tableName, $data) {
        try {
       
            $errors = $this->validateData($tableName, $data, false);
            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }
            
         
            foreach ($data as $field => $value) {
                if (!$this->checkUnique($tableName, $field, $value)) {
                    return ['success' => false, 'errors' => [$field => "El valor '$value' ya existe para el campo '$field'"]];
                }
            }
          
            $schema = $this->tableSchemas[$tableName];
            $filteredData = [];
            foreach ($data as $field => $value) {
                if (!isset($schema[$field]['auto_increment']) || !$schema[$field]['auto_increment']) {
                    $filteredData[$field] = $value;
                }
            }
            
            $fields = array_keys($filteredData);
            $placeholders = array_fill(0, count($filteredData), '?');
            
            $sql = "INSERT INTO $tableName (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute(array_values($filteredData));
            
            if ($result) {
                return ['success' => true, 'id' => $this->db->lastInsertId(), 'message' => 'Registro insertado correctamente'];
            } else {
                return ['success' => false, 'errors' => ['database' => 'Error al insertar el registro']];
            }
            
        } catch (PDOException $e) {
            error_log("Error en insert: " . $e->getMessage());
            return ['success' => false, 'errors' => ['database' => 'Error de base de datos: ' . $e->getMessage()]];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['validation' => $e->getMessage()]];
        }
    }
    
    public function update($tableName, $data, $id) {
        try {
            // Validar datos
            $errors = $this->validateData($tableName, $data, true);
            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }
            
            // Verificar unicidad (excluyendo el registro actual)
            foreach ($data as $field => $value) {
                if (!$this->checkUnique($tableName, $field, $value, $id)) {
                    return ['success' => false, 'errors' => [$field => "El valor '$value' ya existe para el campo '$field'"]];
                }
            }
            
            $primaryKey = $this->getPrimaryKey($tableName);
            
            // Preparar consulta
            $setParts = [];
            foreach (array_keys($data) as $field) {
                $setParts[] = "$field = ?";
            }
            
            $sql = "UPDATE $tableName SET " . implode(', ', $setParts) . " WHERE $primaryKey = ?";
            $params = array_merge(array_values($data), [$id]);
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($params);
            
            if ($result) {
                return ['success' => true, 'message' => 'Registro actualizado correctamente'];
            } else {
                return ['success' => false, 'errors' => ['database' => 'Error al actualizar el registro']];
            }
            
        } catch (PDOException $e) {
            error_log("Error en update: " . $e->getMessage());
            return ['success' => false, 'errors' => ['database' => 'Error de base de datos: ' . $e->getMessage()]];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['validation' => $e->getMessage()]];
        }
    }
    
 
    
public function delete($tableName, $id) {
    try {
        $primaryKey = $this->getPrimaryKey($tableName);
        
        $checkSql = "SELECT COUNT(*) FROM $tableName WHERE $primaryKey = ?";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->execute([$id]);
        
        if ($checkStmt->fetchColumn() == 0) {
            return ['success' => false, 'errors' => ['not_found' => 'El registro no existe']];
        }
        
        $this->db->beginTransaction();
        
        try {
            $this->deleteRelatedRecords($tableName, $id);
            
            $sql = "DELETE FROM $tableName WHERE $primaryKey = ?";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$id]);
            
            if ($result) {
                $this->db->commit();
                return ['success' => true, 'message' => 'Registro y registros relacionados eliminados correctamente'];
            } else {
                $this->db->rollback();
                return ['success' => false, 'errors' => ['database' => 'Error al eliminar el registro']];
            }
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
        
    } catch (PDOException $e) {
        error_log("Error en delete: " . $e->getMessage());
        return ['success' => false, 'errors' => ['database' => 'Error de base de datos: ' . $e->getMessage()]];
    }
}

private function deleteRelatedRecords($tableName, $id) {
    $dependencies = [
        'usuario' => [
            ['table' => 'ticket', 'field' => 'idUsuario']
        ],
        'pasajero' => [
            ['table' => 'ticket', 'field' => 'idPasajero']
        ],
        'bus' => [
            ['table' => 'asiento', 'field' => 'idBus'],
            ['table' => 'trabajadorbus', 'field' => 'idBus'],
            ['table' => 'viajebus', 'field' => 'idBus']
        ],
        'trabajador' => [
            ['table' => 'boletero', 'field' => 'idTrabajador'],
            ['table' => 'trabajadorbus', 'field' => 'idTrabajador'],
            ['table' => 'chofer', 'field' => 'idTrabajador'],
            ['table' => 'terramozo', 'field' => 'idTrabajador']
        ],
        'viaje' => [
            ['table' => 'ticket', 'field' => 'idViaje'],
            ['table' => 'viajeruta', 'field' => 'idViaje'],
            ['table' => 'viajebus', 'field' => 'idViaje']
        ],
        'asiento' => [
            ['table' => 'ticket', 'field' => 'idAsiento']
        ],
        'ruta' => [
            ['table' => 'viajeruta', 'field' => 'idRuta']
        ]
    ];
    
    if (isset($dependencies[$tableName])) {
        foreach ($dependencies[$tableName] as $dependency) {
            $sql = "DELETE FROM {$dependency['table']} WHERE {$dependency['field']} = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
        }
    }
}
    
    private function checkForeignKeyConstraints($tableName, $id) {
        $primaryKey = $this->getPrimaryKey($tableName);
        
        // Definir tablas que referencian a otras (simplificado)
        $references = [
            'usuario' => ['ticket'],
            'pasajero' => ['ticket'],
            'bus' => ['asiento', 'trabajadorbus', 'viajebus'],
            'trabajador' => ['boletero', 'trabajadorbus'],
            'viaje' => ['ticket', 'viajeruta', 'viajebus'],
            'asiento' => ['ticket'],
            'ruta' => ['viajeruta']
        ];
        
        if (isset($references[$tableName])) {
            foreach ($references[$tableName] as $referencingTable) {
                $foreignKeyField = 'id' . ucfirst($tableName);
                
                // Casos especiales
                if ($tableName === 'trabajador' && $referencingTable === 'boletero') {
                    $foreignKeyField = 'idTrabajador';
                } elseif ($tableName === 'trabajador' && $referencingTable === 'trabajadorbus') {
                    $foreignKeyField = 'idTrabajador';
                } elseif ($tableName === 'bus' && $referencingTable === 'trabajadorbus') {
                    $foreignKeyField = 'idBus';
                }
                
                try {
                    $sql = "SELECT COUNT(*) FROM $referencingTable WHERE $foreignKeyField = ?";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([$id]);
                    
                    if ($stmt->fetchColumn() > 0) {
                        return false;
                    }
                } catch (PDOException $e) {
                    // Si la consulta falla, asumir que no hay restricción
                    continue;
                }
            }
        }
        
        return true;
    }
    
    public function getTableSchema($tableName) {
        if (isset($this->tableSchemas[$tableName])) {
            return $this->tableSchemas[$tableName];
        }
        return null;
    }

    public function getForeignKeyOptions($tableName, $primaryKeyField) {
        try {
            $sql = "SELECT * FROM $tableName ORDER BY $primaryKeyField";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getForeignKeyOptions: " . $e->getMessage());
            return [];
        }
    }
    

    public function getAvailableTables() {
        return array_keys($this->tableSchemas);
    }
    
    public function getById($tableName, $id) {
        try {
            $primaryKey = $this->getPrimaryKey($tableName);
            $sql = "SELECT * FROM $tableName WHERE $primaryKey = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getById: " . $e->getMessage());
            return false;
        }
    }
}
?>