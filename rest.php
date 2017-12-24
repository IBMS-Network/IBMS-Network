<?php

// set timezone
ini_set('date.timezone', 'Europe/Moscow');
// set russian utf8 headers for web server
putenv("LC_ALL=ru_RU");
setLocale(LC_ALL, 'ru_RU.utf8');

ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('display_errors', 'on');

$loader = require_once __DIR__ . '/vendor/autoload.php';

// init main file config
require_once (realpath(dirname(__FILE__)) . "/config/config.php");

// module init name
$modules_names = array('mobile', 'rest');

// try to add engine
$error_message = '';
try {
    if (!file_exists(ENGINE_PATH . "sys_index.php")) {
        throw new \Exception('<br>Engine is not find by path [' . ENGINE_PATH . ']<br>');
    } else {
        require_once (ENGINE_PATH . "sys_index.php");
    }
} catch (\Exception $e) {
    $error_message = '<br>System is down. Please contact to support. Type Error : 2.<br>';
    if (USE_DEBUG) {

        $error_message .= $e->getMessage();
    }

    // exit with simple output
    exit($error_message);
}

// set timer for page loading statistic in debag mode
$startTime = '';
if (USE_DEBUG) {
    $startTime = array_sum(explode(" ", microtime()));
}

$error_message = '';
$throw_mess = '';
try {

    if (!file_exists(SERVER_ROOT . "bootstrap.php")) {
        $throw_mess = '<br> bootstrap file in project not find [' . SERVER_ROOT . "bootstrap.php" . ']<br>';
        throw new \Exception($throw_mess);
    } else {
        require_once (SERVER_ROOT . "bootstrap.php");
    }

    // try to execute start application
    if (class_exists('engine\modules\rest\clsRestCore')) {
        $objCore = new engine\modules\rest\clsRestCore();
        $objCore->runApp();
    } else {
        $throw_mess = '<br> Class [clsMobCore] is not init.<br>';
        throw new \Exception($throw_mess);
    }
} catch (\Exception $e) {
    $error_message = '<br>System is down. Please contact to support. Type Error : 3.<br>';
    if (USE_DEBUG) {

        $error_message .= $e->getMessage();
    }
    classes\core\clsCommon::debugMessage($e->getMessage(), __FILE__, false, true);
    exit();
}



// output timer of page loading in debug mode
if (USE_DEBUG) {
    echo "<br /><br />";
    echo round((array_sum(explode(" ", microtime())) - $startTime), 4) . ' sec';
}
