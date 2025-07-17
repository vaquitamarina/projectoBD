<?php

class AppController {
    private $model;
    function __construct() {
        $this->model = new Modelo();
    }

    public function renderView($viewName) {
        if (file_exists("views/{$viewName}.php")) {
            require_once("views/{$viewName}.php");
        } else {
            throw new Exception("Vista no encontrada: {$viewName}");
        }
    }
}

?>
