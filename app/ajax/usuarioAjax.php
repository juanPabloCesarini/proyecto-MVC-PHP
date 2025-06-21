<?php
    require_once "../../config/app.php";
    require_once "../views/inc/session_start.php";
    require_once "../../autoload.php";

    use app\controllers\userController;

    if (isset($_POST['modulo_usuario'])) {
        $instanciaUsuario = new userController;
        if($_POST['modulo_usuario']=="registrar"){
            echo $instanciaUsuario->registrarUsuarioControlador();
        }
    } else {
        session_destroy();
        header("Location: ".APP_URL. "login/");
    }
    