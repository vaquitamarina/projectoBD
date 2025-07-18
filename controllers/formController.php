<?php
require_once("../models/modelForm.php");

class formController {
    private $model;
    
    public function __construct() {
        $this->model = new ModelForm();
    }
    
    public function generateForm($tableName, $editData = null) {
        $schema = $this->model->getTableSchema($tableName);
        
        if (!$schema) {
            return "<p>Error: Tabla '$tableName' no encontrada</p>";
        }
        
        $isEditMode = !empty($editData);
        $formTitle = $isEditMode ? "Editar registro de $tableName" : "Formulario para $tableName";
        
        $html = "<form method='POST' action='../controllers/processForm.php'>";
        $html .= "<input type='hidden' name='table' value='$tableName'>";
        if ($isEditMode) {
            $html .= "<input type='hidden' name='editMode' value='true'>";
        }
        $html .= "<h2>$formTitle</h2>";
        
        foreach ($schema as $field => $rules) {
            // Saltar campos auto_increment en modo edición también
            if (isset($rules['auto_increment']) && $rules['auto_increment']) {
                // En modo edición, agregar el ID como campo oculto
                if ($isEditMode && isset($editData[$field])) {
                    $html .= "<input type='hidden' name='$field' value='" . htmlspecialchars($editData[$field]) . "'>";
                }
                continue;
            }
            
            $html .= "<div class='form-group'>";
            $html .= "<label for='$field'>" . ucfirst($field);
            if (isset($rules['required']) && $rules['required']) {
                $html .= " *";
            }
            $html .= ":</label>";
            
            // Obtener valor actual si está en modo edición
            $currentValue = $isEditMode && isset($editData[$field]) ? $editData[$field] : '';
            
            // Verificar si es clave foránea
            if (isset($rules['foreign_key'])) {
                $html .= $this->generateForeignKeySelect($field, $rules, $currentValue);
            } else {
                switch ($rules['type']) {
                    case 'email':
                        $html .= "<input type='email' id='$field' name='$field' value='" . htmlspecialchars($currentValue) . "'";
                        if (isset($rules['required']) && $rules['required']) {
                            $html .= " required";
                        }
                        if (isset($rules['max_length'])) {
                            $html .= " maxlength='{$rules['max_length']}'";
                        }
                        $html .= ">";
                        break;
                    case 'date':
                        $html .= "<input type='date' id='$field' name='$field' value='" . htmlspecialchars($currentValue) . "'";
                        if (isset($rules['required']) && $rules['required']) {
                            $html .= " required";
                        }
                        $html .= ">";
                        break;
                    case 'time':
                        $html .= "<input type='time' id='$field' name='$field' value='" . htmlspecialchars($currentValue) . "'";
                        if (isset($rules['required']) && $rules['required']) {
                            $html .= " required";
                        }
                        $html .= ">";
                        break;
                    case 'int':
                        $html .= "<input type='number' id='$field' name='$field' value='" . htmlspecialchars($currentValue) . "'";
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
                                // Comparación más robusta que maneja strings y números
                                $selected = (strval($currentValue) == strval($value)) ? " selected" : "";
                                $html .= "<option value='$value'$selected>$value</option>";
                            }
                            $html .= "</select>";
                            
                            // Debug temporal (comentar en producción)
                            if ($isEditMode && !empty($currentValue)) {
                                $html .= "<!-- Debug: Campo '$field' - Valor actual: '$currentValue' - Valores permitidos: " . implode(', ', $rules['allowed_values']) . " -->";
                            }
                        } else {
                            // Input text normal
                            $html .= "<input type='text' id='$field' name='$field' value='" . htmlspecialchars($currentValue) . "'";
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
        
        $submitText = $isEditMode ? "Actualizar" : "Guardar";
        $html .= "<button type='submit'>$submitText</button>";
        $html .= "</form>";
        
        return $html;
    }
    
    /**
     * Genera un select para claves foráneas con datos de la tabla referenciada
     */
    private function generateForeignKeySelect($field, $rules, $currentValue = '') {
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
                $selected = ($currentValue == $value) ? " selected" : "";
                $displayText = $this->getDisplayText($referencedTable, $row);
                $html .= "<option value='$value'$selected>$displayText</option>";
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