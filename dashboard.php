<?php
require_once 'controllers/BancoController.php';
$controller = new BancoController();
$datos = $controller->procesarOperacion();

require_once 'views/dashboard_view.php';