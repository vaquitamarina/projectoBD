<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISTEMA TRANSPORTE</title>
    <script src="https://kit.fontawesome.com/f9a3e96628.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/crud.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body>
    <div class="container">
        <header>
            <h1>Sistema de Transportes</h1>
        </header>
        <main>
            <?php
            error_reporting(E_ALL);
            ini_set('display_errors', 1);

            require_once("../controllers/controller.php");

            try {
                $controller = new ModelController();
                if(isset($_POST['accion'])){
                    switch($_POST['accion']){
                        case 'getRegister':
                            $controller->select();
                            break;
                        case 'addForm':
                            $controller->tableForm();
                            break;
                        case 'updateRow':
                            $data = $_POST;
                            $controller->updateRow($data);
                            break;
                        case 'addRow':
                            $data = $_POST;
                            $controller->addRow($data);
                            break;
                        case 'deleteRow':
                            $data = $_POST;
                            $controller->deleteRow($data);
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
        <footer>
            <a class="button-return" href="crud.php">Volver</a>
            <a class="button-return" href="../index.php">Inicio</a>
        </footer>
    </div>
</body>

</html>