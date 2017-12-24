<?php

use engine\clsSysCommon;
use engine\clsSysSession;

// init two main files : config and common static class
require_once (realpath(dirname(__FILE__)) . "/sysconfig/config.php");

// set error handler
set_error_handler(array('engine\clsSysCommon', 'errorHandler'));

$error_message = '';
try {

    // add bootstrap file
    if (!file_exists(SERVER_SYS_ROOT . "sys_bootstrap.php")) {
        $search = array('{__sys_path__}');
        $repl = array(SERVER_SYS_ROOT);
        $error_message = clsSysCommon::getMessage('boot_not_find', 'Errors', $search, $repl);
        throw new \Exception($error_message);
    } else {
        require_once (SERVER_SYS_ROOT . "sys_bootstrap.php");
    }

    // set history for user navigation
    if (class_exists('engine\clsSysSession')) {
        clsSysSession::saveLastVisitedURL();
    } else {
        $search = array('{__cls_name__}');
        $repl = array('clsSysSession');
        $error_message = clsSysCommon::getMessage('cls_is_missed', 'Errors', $search, $repl);
        throw new \Exception($error_message);
    }
} catch (\Exception $e) {

    $error_message = clsSysCommon::getMessage('system_down', 'Errors');
    if (clsSysCommon::getCommonDebug()) {
        $error_message .= $e->getMessage();
    }
    clsSysCommon::debugMessage($error_message, "Engine : Index : Exeption", false, true);
    exit();
}