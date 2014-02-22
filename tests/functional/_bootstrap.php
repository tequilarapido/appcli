<?php

// Autoload
if (!defined('TEQ_PROJECT_ROOT')) {
    define('TEQ_PROJECT_ROOT', realpath(__DIR__ . '/../../'));
    require_once TEQ_PROJECT_ROOT . '/src/vendor/autoload.php';

    // Environnement variables from .env file ?
    if (is_file(TEQ_PROJECT_ROOT . '/.env')) {
        Dotenv::load(TEQ_PROJECT_ROOT);
    }
}
