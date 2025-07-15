<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISTEMA TRANSPORTE</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>

<body>
    <header>
        <h1>Sistema de Transportes</h1>
    </header>
    <main>
        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        require_once("controllers/controller.php");

        try {
            $controller = new ModelController();
            if(isset($_POST['accion'])){
                switch($_POST['accion']){
                    case 'getRegister':
                        $controller->select();
                        break;
                    case 'addRow':
                        $controller->addRow();
                        break;
                }
            }else{
                $controller->select();
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            include "views/select.php";
        }
        ?>
    </main>
</body>

</html>