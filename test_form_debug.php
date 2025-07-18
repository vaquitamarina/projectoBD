<?php
// Archivo de prueba para verificar el problema con allowed_values
require_once("../models/modelForm.php");
require_once("../controllers/formController.php");

echo "<h2>Prueba de Formulario con Valores Permitidos</h2>";

// Simular datos de edición
$testEditData = [
    'idPasajero' => '1',
    'nombres' => 'Juan',
    'apellidos' => 'Pérez',
    'email' => 'juan@email.com',
    'fechaDeNacimiento' => '1990-01-01',
    'sexo' => 'M',  // Este debería estar seleccionado
    'dni' => '12345678'
];

echo "<h3>Datos de prueba:</h3>";
echo "<pre>";
print_r($testEditData);
echo "</pre>";

$formController = new formController();
$formHtml = $formController->generateForm('pasajero', $testEditData);

echo "<h3>Formulario generado:</h3>";
echo $formHtml;

echo "<h3>Verificación de valores permitidos:</h3>";
$model = new ModelForm();
$schema = $model->getTableSchema('pasajero');
if ($schema && isset($schema['sexo']['allowed_values'])) {
    echo "Valores permitidos para 'sexo': ";
    print_r($schema['sexo']['allowed_values']);
    echo "<br>";
    echo "Valor actual: '" . $testEditData['sexo'] . "'<br>";
    echo "Comparación con 'M': " . (strval($testEditData['sexo']) == strval('M') ? "TRUE" : "FALSE") . "<br>";
    echo "Comparación con 'F': " . (strval($testEditData['sexo']) == strval('F') ? "TRUE" : "FALSE") . "<br>";
}
?>
