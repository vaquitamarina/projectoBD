<?php
$tableName = $data;
require_once("../controllers/formController.php");

$formController = new formController();

echo $formController->generateForm($tableName);

?>