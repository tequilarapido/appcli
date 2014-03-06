<?php

if (!defined('TEQ_PROJECT_ROOT')) {

    /*
    |--------------------------------------------------------------------------
    |  Autoload
    |--------------------------------------------------------------------------
    |
    */
    define('TEQ_PROJECT_ROOT', realpath(__DIR__ . '/../../'));
    require_once TEQ_PROJECT_ROOT . '/src/vendor/autoload.php';

    /*
    |--------------------------------------------------------------------------
    |   Environnement variables from .env file ?
    |--------------------------------------------------------------------------
    |
    */
    if (is_file(TEQ_PROJECT_ROOT . '/.env')) {
        Dotenv::load(TEQ_PROJECT_ROOT);
    }

    /*
    |--------------------------------------------------------------------------
    |  Mailcatcher check
    |--------------------------------------------------------------------------
    | Make sure mailcatcher is running before trying to launch test
    | as it is required
    |
    */
    $mailcatcher = new Guzzle\Http\Client('http://127.0.0.1:1080');
    try {
        $mailcatcher->get('/messages')->send();
    } catch (\Exception $e) {
        $msg = "\n\n\n!!! Error !!!";
        $msg .= "\nYou need to launch mailcatcher to be able to run tests.";
        $msg .= "\nOriginal exception message : " . $e->getMessage();
        die($msg);
    }
}
