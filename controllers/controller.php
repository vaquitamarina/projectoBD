<?php
require_once("../models/model.php");
require_once("../models/modelForm.php");

class ModelController{
    private $model;
    private $modelForm;
    function __construct(){
        $this->model = new Modelo();
        $this->modelForm = new ModelForm();
    }
    public function renderView($viewName, $data = null, $registros = null){
        if (file_exists("{$viewName}.php")) {
            require_once("{$viewName}.php");
        } else {
            throw new Exception("Vista no encontrada: {$viewName}");
        }
    }
    public function select(){
        $data = $this->model->getTables();
        $registros = null;
        if (isset($_POST['accion']) && $_POST['accion'] === 'getRegister' && !empty($_POST['selectedTable'])) {
                $selectedTable = $_POST['selectedTable'];

                if (in_array($selectedTable, $data)) {
                    $registros = $this->model->getAll($selectedTable);
                } else {
                    $error = "Tabla no válida";
                }
            }
        $this->renderView("select", $data, $registros);
    }
    public function tableForm(){
        $table = $_POST['insertTable'];
        $this->renderView("form", $table);
    }
    public function addRow($data){
        $tableName = $data['table'];
        $values = $data;
        unset($values['table']);
        unset($values['accion']);

        $result = $this->modelForm->insert($tableName, $values);
    
        if ($result && isset($result['success']) && $result['success']) {
            // Redirigir a crud.php con POST para mostrar la tabla actualizada
            echo "<script>
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'crud.php';
                
                const tableField = document.createElement('input');
                tableField.type = 'hidden';
                tableField.name = 'selectedTable';
                tableField.value = '" . htmlspecialchars($tableName) . "';
                form.appendChild(tableField);
                
                const actionField = document.createElement('input');
                actionField.type = 'hidden';
                actionField.name = 'accion';
                actionField.value = 'getRegister';
                form.appendChild(actionField);
                
                document.body.appendChild(form);
                form.submit();
            </script>";
            exit;
        } else {
            echo "Error en la actualización";
            if (isset($result['errors'])) {
                print_r($result['errors']);
            }
            $this->renderView("form", $tableName);
        }  
    }

    public function updateRow($data){
        $tableName = $data['table'];
        $primaryKey = $this->modelForm->getPrimaryKey($tableName);
        $id = $data[$primaryKey];

        $values = $data;
        unset($values['editMode']);
        unset($values['table']);
        unset($values[$primaryKey]);
        unset($values['accion']);

        $result = $this->modelForm->update($tableName, $values, $id);
    
    if ($result && isset($result['success']) && $result['success']) {
        // Redirigir a crud.php con POST para mostrar la tabla actualizada
        echo "<script>
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'crud.php';
            
            const tableField = document.createElement('input');
            tableField.type = 'hidden';
            tableField.name = 'selectedTable';
            tableField.value = '" . htmlspecialchars($tableName) . "';
            form.appendChild(tableField);
            
            const actionField = document.createElement('input');
            actionField.type = 'hidden';
            actionField.name = 'accion';
            actionField.value = 'getRegister';
            form.appendChild(actionField);
            
            document.body.appendChild(form);
            form.submit();
        </script>";
        exit;
    } else {
        echo "Error en la actualización";
        if (isset($result['errors'])) {
            print_r($result['errors']);
        }
        $this->renderView("form", $tableName);
    }

    }

    public function deleteRow($data){
        $tableName = $data['insertTable'];
        $primaryKey = $this->modelForm->getPrimaryKey($tableName);
        $values = $data['editData'];
        $id = $values[$primaryKey];
        $this->modelForm->delete($tableName,$id);
        echo "<script>
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'crud.php';
            
            const tableField = document.createElement('input');
            tableField.type = 'hidden';
            tableField.name = 'selectedTable';
            tableField.value = '" . htmlspecialchars($tableName) . "';
            form.appendChild(tableField);
            
            const actionField = document.createElement('input');
            actionField.type = 'hidden';
            actionField.name = 'accion';
            actionField.value = 'getRegister';
            form.appendChild(actionField);
            
            document.body.appendChild(form);
            form.submit();
        </script>";
        exit;
    }
}

?>