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
            
            // Generar input según el tipo
            switch ($rules['type']) {
                case 'email':
                    $html .= "<input type='email' id='$field' name='$field'";
                    break;
                case 'date':
                    $html .= "<input type='date' id='$field' name='$field'";
                    break;
                case 'time':
                    $html .= "<input type='time' id='$field' name='$field'";
                    break;
                case 'int':
                    $html .= "<input type='number' id='$field' name='$field'";
                    if (isset($rules['min_value'])) {
                        $html .= " min='{$rules['min_value']}'";
                    }
                    if (isset($rules['max_value'])) {
                        $html .= " max='{$rules['max_value']}'";
                    }
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
                    }
                    break;
            }
            
            if (!isset($rules['allowed_values'])) {
                if (isset($rules['required']) && $rules['required']) {
                    $html .= " required";
                }
                $html .= ">";
            }
            
            $html .= "</div>";
        }
        
        $html .= "<button type='submit'>Guardar</button>";
        $html .= "</form>";
        
        return $html;
    }
}
?>