<?php
require_once("../models/modelForm.php");

class formController {
    private $model;
    
    public function __construct() {
        $this->model = new ModelForm();
    }
    
    public function generateForm($tableName) {
        $schema = $this->model->getTableSchema($tableName);
        
        if (!$schema) {
            return "<p>Error: Tabla '$tableName' no encontrada</p>";
        }
        
        $html = "<form method='POST' action='../controllers/processForm.php'>";
        $html .= "<input type='hidden' name='table' value='$tableName'>";
        $html .= "<h2>Formulario para $tableName</h2>";
        
        foreach ($schema as $field => $rules) {
            // Saltar campos auto_increment
            if (isset($rules['auto_increment']) && $rules['auto_increment']) {
                continue;
            }
            
            $html .= "<div class='form-group'>";
            $html .= "<label for='$field'>" . ucfirst($field);
            if (isset($rules['required']) && $rules['required']) {
                $html .= " *";
            }
            $html .= ":</label>";
            
            // Verificar si es clave foránea
            if (isset($rules['foreign_key'])) {
                $html .= $this->generateForeignKeySelect($field, $rules);
            } else {
                switch ($rules['type']) {
                    case 'email':
                        $html .= "<input type='email' id='$field' name='$field'";
                        if (isset($rules['required']) && $rules['required']) {
                            $html .= " required";
                        }
                        if (isset($rules['max_length'])) {
                            $html .= " maxlength='{$rules['max_length']}'";
                        }
                        $html .= ">";
                        break;
                    case 'date':
                        $html .= "<input type='date' id='$field' name='$field'";
                        if (isset($rules['required']) && $rules['required']) {
                            $html .= " required";
                        }
                        $html .= ">";
                        break;
                    case 'time':
                        $html .= "<input type='time' id='$field' name='$field'";
                        if (isset($rules['required']) && $rules['required']) {
                            $html .= " required";
                        }
                        $html .= ">";
                        break;
                    case 'int':
                        $html .= "<input type='number' id='$field' name='$field'";
                        if (isset($rules['min_value'])) {
                            $html .= " min='{$rules['min_value']}'";
                        }
                        if (isset($rules['max_value'])) {
                            $html .= " max='{$rules['max_value']}'";
                        }
                        if (isset($rules['required']) && $rules['required']) {
                            $html .= " required";
                        }
                        $html .= ">";
                        break;
                    default:
                        if (isset($rules['allowed_values'])) {
                            // Select para valores permitidos
                            $html .= "<select id='$field' name='$field'";
                            if (isset($rules['required']) && $rules['required']) {
                                $html .= " required";
                            }
                            $html .= ">";
                            $html .= "<option value=''>Seleccionar...</option>";
                            foreach ($rules['allowed_values'] as $value) {
                                $html .= "<option value='$value'>$value</option>";
                            }
                            $html .= "</select>";
                        } else {
                            // Input text normal
                            $html .= "<input type='text' id='$field' name='$field'";
                            if (isset($rules['max_length'])) {
                                $html .= " maxlength='{$rules['max_length']}'";
                            }
                            if (isset($rules['required']) && $rules['required']) {
                                $html .= " required";
                            }
                            $html .= ">";
                        }
                        break;
                }
                
            }
            
            $html .= "</div>";
        }
        
        $html .= "<button type='submit'>Guardar</button>";
        $html .= "</form>";
        
        return $html;
    }
    
    /**
     * Genera un select para claves foráneas con datos de la tabla referenciada
     */
    private function generateForeignKeySelect($field, $rules) {
        // Extraer el nombre de la tabla y campo de la foreign key
        list($referencedTable, $referencedField) = explode('.', $rules['foreign_key']);
        
        // Obtener los datos de la tabla referenciada
        $data = $this->model->getForeignKeyOptions($referencedTable, $referencedField);
        
        $html = "<select id='$field' name='$field'";
        if (isset($rules['required']) && $rules['required']) {
            $html .= " required";
        }
        $html .= ">";
        $html .= "<option value=''>Seleccionar " . ucfirst($referencedTable) . "...</option>";
        
        if ($data) {
            foreach ($data as $row) {
                $value = $row[$referencedField];
                $displayText = $this->getDisplayText($referencedTable, $row);
                $html .= "<option value='$value'>$displayText</option>";
            }
        }
        
        $html .= "</select>";
        return $html;
    }
    
    /**
     * Genera texto descriptivo para mostrar en el select
     *

     * Genera texto descriptivo para mostrar en el select
     */
    private function getDisplayText($tableName, $row) {
        switch ($tableName) {
            case 'pasajero':
                return 'ID: ' . $row['idPasajero'] . ' - ' . $row['nombres'] . ' ' . $row['apellidos'] . ' (' . $row['email'] . ')';
            case 'trabajador':
                return 'ID: ' . $row['idTrabajador'] . ' - ' . $row['nombres'] . ' ' . $row['apellidos'] . ' - DNI: ' . ($row['dni'] ?? 'N/A');
            case 'bus':
                return 'ID: ' . $row['idBus'] . ' - Placa: ' . $row['placa'] . ' - ' . $row['clase'] . ' (' . $row['nAsientos'] . ' asientos)';
            case 'viaje':
                return 'ID: ' . $row['idViaje'] . ' - Fecha: ' . $row['fecha'] . ' - ' . $row['hInicio'] . ' a ' . $row['hFinal'];
            case 'asiento':
                return 'ID: ' . $row['idAsiento'] . ' - Asiento #' . $row['numeroAsiento'] . ' - Piso ' . $row['piso'];
            case 'ruta':
                return 'ID: ' . $row['idRuta'] . ' - ' . $row['ciudadFinal'];
            default:
                // Para cualquier tabla no especificada, usar el primer campo no-ID
                $idField = array_keys($row)[0]; // Primer campo (generalmente el ID)
                $idValue = $row[$idField];
                
                foreach ($row as $field => $value) {
                    if (strpos($field, 'id') !== 0 && !empty($value)) {
                        return 'ID: ' . $idValue . ' - ' . $value;
                    }
                }
                return 'ID: ' . $idValue; // Solo mostrar ID como fallback
        }
    }

// ...existing code...
}
?>