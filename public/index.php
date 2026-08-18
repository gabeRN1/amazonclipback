<?php
// Definição da URL Base absoluta para evitar loops e erros de rota
define('BASE_URL', 'https://amazonpicture.com.br/dash-trabalheconosco');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

spl_autoload_register(function ($class) {
    $dirs = ['../app/Core/', '../app/Controllers/'];
    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

$app = new App();