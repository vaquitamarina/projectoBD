<?php
$tableName = $data;
require_once("../controllers/formController.php");

$formController = new formController();

// Verificar si estamos en modo edición
$editData = null;
if (isset($_POST['editMode']) && $_POST['editMode'] === 'true' && isset($_POST['editData'])) {
    $editData = $_POST['editData'];
}

echo $formController->generateForm($tableName, $editData);

?>
