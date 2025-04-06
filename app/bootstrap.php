<?php
// Load Config
require_once '../config/config.php';

// Load Helper
require_once 'core/Helper.php';

// Autoload Core Libraries
spl_autoload_register(function($className) {
    require_once 'core/' . $className . '.php';
});

// Start Session
session_start(); 