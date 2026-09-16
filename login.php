<?php
require_once 'controllers/BancoController.php';
$controller = new BancoController();
$error = $controller->iniciarSesion();

require_once 'views/login_view.php';