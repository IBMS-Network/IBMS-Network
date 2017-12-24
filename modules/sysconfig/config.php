<?php
// +++++ SERVER +++++
define ( "SERVER_SYS_ROOT", realpath ( dirname ( __FILE__ ) . "/.." ) . "/" );
// ----- SERVER -----

// ENGINE CONSTANTS
define ( "USE_SYS_DEBUG", 1 );

// +++++ PATH +++++
define ( "CONF_SYS_PATH", SERVER_SYS_ROOT . "sysconfig/" );
define ( "LOG_SYS_PATH", SERVER_SYS_ROOT . "syslogs/" );
define ( "CLS_SYS_PATH", SERVER_SYS_ROOT . "sysclasses/" );
define ( "COMMON_SYS_CLS_PATH", CLS_SYS_PATH . "core/" );
define ( "DICTIONARY_SYS_PATH", SERVER_SYS_ROOT . "sysdictionary/" );
define ( "MODULES_SYS_PATH", SERVER_SYS_ROOT . "sysmodules/" );
if(!defined('CORE_3RDPARTY_PATH')) {
    define ( "CORE_3RDPARTY_PATH", SERVER_SYS_ROOT . "../3rdparty/" );
}

if (!empty($modules_names) && is_array($modules_names)){
    foreach ($modules_names As $moduleName){
        define ( "MODULE_" . strtoupper($moduleName) . "_CLS_SYS_PATH", MODULES_SYS_PATH . $moduleName . "/sysclasses/" );
        define ( "MODULE_" . strtoupper($moduleName) . "_COMMON_SYS_CLS_PATH", MODULES_SYS_PATH . $moduleName . "/sysclasses/core/" );
        define ( "MODULE_" . strtoupper($moduleName) . "_CONFIG_PATH", MODULES_SYS_PATH . $moduleName . "/sysconfig/" );
        define ( "MODULE_" . strtoupper($moduleName) . "_DICTIONARY_PATH", MODULES_SYS_PATH . $moduleName . "/sysdictionary/" );
    }
}

define ( "MOBILE_LOGIN_REQUIRED", 1 );