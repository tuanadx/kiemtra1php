<?php
// Load DotEnv
require_once __DIR__ . '/core/DotEnv.php';
(new DotEnv(dirname(__DIR__) . '/.env'))->load();

// Load Config
$appRoot = dirname(__DIR__);
require_once $appRoot . '/config/config.php';

// Load Helper
require_once $appRoot . '/app/core/Helper.php';

// Autoload Core Libraries
spl_autoload_register(function($className) {
    $appRoot = dirname(__DIR__);
    if (file_exists($appRoot . '/app/core/' . $className . '.php')) {
        require_once $appRoot . '/app/core/' . $className . '.php';
    } elseif (file_exists($appRoot . '/app/controllers/' . $className . '.php')) {
        require_once $appRoot . '/app/controllers/' . $className . '.php';
    } elseif (file_exists($appRoot . '/app/models/' . $className . '.php')) {
        require_once $appRoot . '/app/models/' . $className . '.php';
    }
});

// Start Session
session_start(); 