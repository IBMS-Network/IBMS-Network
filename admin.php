<?php
// set timezone
ini_set('date.timezone', 'Europe/Moscow');
ini_set('display_errors',1);
error_reporting(E_ALL);

// set russian utf8 headers for web server
putenv("LC_ALL=ru_RU");
setLocale(LC_ALL, 'ru_RU.utf8');
if (check_session_start()) {
    session_start();
}

$loader = require_once __DIR__ . '/vendor/autoload.php';

Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace(
    'Gedmo\Mapping\Annotation',
    __DIR__ . '/vendor'
);

/**
 * @todo maybe move to composer.json as files param?
 */
require_once (__DIR__ . "/config/config.php");

// module init name
$modules_names = array('general', 'admin');

// try to add engine
$error_message = '';
try {
    if (!file_exists(ENGINE_PATH . "sys_index.php")) {
        throw new \Exception('<br>Engine is not find by path [' . ENGINE_PATH . ']<br>');
    } else {
        require_once (ENGINE_PATH . "sys_index.php");
    }
} catch( \Exception $e ) {
    $error_message = '<br>System is down. Please contact to support. Type Error : 2.<br>';
    if (USE_DEBUG) {

        $error_message .= $e->getMessage();
    }

    // exit with simple output
    exit($error_message);

}

// init clsCommon file
//require_once (COMMON_CLS_PATH . "clsCommon.php");

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

    // try to init method saveLastVisitedURL from clsSession
    if (class_exists('classes\clsSession')) {
        classes\clsSession::saveLastVisitedURL();
    } else {
        $throw_mess = '<br> Class [clsSession] is not init.<br>';
        throw new \Exception($throw_mess);
    }

    // try to execute start application
    if (class_exists('engine\modules\admin\clsAdminCore')) {
        $objCore = new engine\modules\admin\clsAdminCore();
        $objCore->runApp();
    } else {
        $throw_mess = '<br> Class [clsAdminCore] is not init.<br>';
        throw new \Exception($throw_mess);
    }
} catch( \Exception $e ) {
    $error_message = '<br>System is down. Please contact to support. Type Error : 3.<br>';
    if (USE_DEBUG) {

        $error_message .= $e->getMessage();
    }
    classes\core\clsCommon::debugMessage($e->getMessage(), __FILE__, false, true);
    exit();
}
// output timer of page loading in debug mode
if (USE_DEBUG) {
//    echo "<br /><br />";
//    echo round((array_sum(explode(" ", microtime())) - $startTime), 4) . ' sec';
}

function check_session_start() {
    if (stripos($_SERVER['HTTP_USER_AGENT'], "Googlebot/2.1") !== false)
        return false;
    if (stripos($_SERVER['HTTP_USER_AGENT'], "msnbot") !== false)
        return false;
    if (stripos($_SERVER['HTTP_USER_AGENT'], "Alexa") !== false)
        return false;
    if (stripos($_SERVER['HTTP_USER_AGENT'], "Yahoo") !== false)
        return false;
    if (stripos($_SERVER['HTTP_USER_AGENT'], "Ask/Teoma") !== false)
        return false;
    if (stripos($_SERVER['HTTP_USER_AGENT'], "Jeeves") !== false)
        return false;
    return true;
}