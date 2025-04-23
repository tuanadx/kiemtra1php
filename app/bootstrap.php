<?php
// Load Config
$appRoot = dirname(__DIR__);
require_once $appRoot . '/config/config.php';

// Load Helper
require_once $appRoot . '/app/core/Helper.php';

// Autoload Core Libraries
spl_autoload_register(function($className) {
    $appRoot = dirname(__DIR__);
    require_once $appRoot . '/app/core/' . $className . '.php';
});

// Start Session
session_start(); 