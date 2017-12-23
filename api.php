<?php

set_time_limit(0);
define('API_MODE', 1);

ini_set('date.timezone', 'Europe/Moscow');

setLocale(LC_ALL, 'ru_RU.utf8');

$startTime = array_sum(explode(" ", microtime()));
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
require_once( realpath(dirname(__FILE__)) . "/config/config.php" );

function apiCoreShutdown() {
    global $core;
    $core->shutdown();
}

class clsApiAutoLoader {

    /**
    * File exists check
    * 
    * @param string $fileName
    * 
    * @return bool
    */
    static function fileExists($fileName){
        $result = false;

        static $filesExistsCache = array();
        
        if (isset($filesExistsCache[$fileName])) {
            $result = $filesExistsCache[$fileName];
        } else {
            $result = $filesExistsCache[$fileName] = file_exists($fileName);
        }

        return $result;
    }
    
    public static function register() {
        return spl_autoload_register(array(__CLASS__, 'loader'));
    }

    public static function unregister() {
        return spl_autoload_unregister(array(__CLASS__, 'loader'));
    }

    public static function loader($className) {
        if ((strpos($className, 'clsApi') === 0)) {// load api classes
            $fileName = COMMON_CLS_PATH . 'api/' . $className . '.php';
            
            if (self::fileExists($fileName)) {
                require_once($fileName);
            } elseif ((strpos($className, 'clsApiAction') !== false)) {
                $fileName = COMMON_CLS_PATH . 'api/actions/' . $className . '.php';
                if (self::fileExists($fileName)) {
                    require_once($fileName);
                }
            } elseif ((strpos($className, 'clsApiNode') !== false)) {
                $fileName = COMMON_CLS_PATH . 'api/nodes/' . $className . '.php';
                if (self::fileExists($fileName)) {
                    require_once($fileName);
                }
            }
        } elseif ((strpos($className, 'cls') === 0)) { // load project classes
            $fileName = CLS_PATH . '/' . $className . '.php';
            if (self::fileExists($fileName)) {
                require_once($fileName);
            } else {
                $fileName = COMMON_CLS_PATH . '/' . $className . '.php';
                if (self::fileExists($fileName)) {
                    require_once($fileName);
                }
            }
        } else { // load other classes
            $fileName = COMMON_CLS_PATH . '/cls' . $className . '.php';
            if (self::fileExists($fileName)) {
                require_once($fileName);
            } else {
                $fileName = CLS_PATH . '/cls' . $className . '.php';
                if (self::fileExists($fileName)) {
                    require_once($fileName);
                }
            }
        }
    }

}

try {
    require_once( COMMON_CLS_PATH . "clsApiCore.php" );
    require_once( DICTIONARY_PATH . "errtext.php" );

    $core = new clsApiCore();
    register_shutdown_function('apiCoreShutdown');

    // try to add engine
    if (! file_exists ( ENGINE_PATH . "sys_index.php" )) {
        throw new \Exception ( 'Engine is not find by path [' . ENGINE_PATH . ']<br>' );
    } else {
        require_once (ENGINE_PATH . "sys_index.php");
    }

    clsApiAutoLoader::register();
    $core->runApp();
    clsSession::saveLastVisitedURL();

} catch (\Exception $e) {
    if (USE_DEBUG) {
        
        if (class_exists('clsCommon')) {
            clsCommon::debugMessage($e->getMessage(), "api.php::Exeption");
        } else {
            throw $e;
        }
    } else {
        exit("System is down. Please contact to support.");
    }
}

