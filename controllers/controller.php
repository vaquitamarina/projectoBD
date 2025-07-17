<?php
require_once("models/model.php");

class ModelController{
    private $model;
    function __construct(){
        $this->model = new Modelo();
    }
    public function renderView($viewName, $data = null, $registros = null){
        if (file_exists("views/{$viewName}.php")) {
            require_once("views/{$viewName}.php");
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
    public function addRow(){
        $table = $_POST['insertTable'];
        $row = $_POST;
        unset($row['insertTable']);
        unset($row['accion']);
        $column = array_keys($row);
        $row = array_values($row);
        $result = $this->model->insert($table, $column, $row);
        if ($result) {
            // Mensaje de éxito en sesión
            echo "<script>alert('Registro insertado correctamente');</script>";
            header("Location: index.php?action=select&table=" . urlencode($table));
        } else {
            // Mensaje de error en sesión
            echo "<script>alert('Error al insertar el registro');</script>";
            header("Location: index.php?action=select");
        }
        exit();          
    }    
}

?>