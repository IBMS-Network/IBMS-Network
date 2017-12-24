<?php
date_default_timezone_set('Europe/Minsk');

session_start();
ini_set('display_errors', 1);
//		ini_set('log_errors', 1);
error_reporting(E_ALL);

require_once(realpath(dirname(dirname(__FILE__))) . "/config/config.php");
//      require_once( COMMON_CLS_PATH . "clsSession.php" );
require_once(COMMON_CLS_PATH . "clsCommon.php");
require_once(COMMON_CLS_PATH . "clsDB.php");
require_once(CLS_PATH . "clsYML.php");

$objSiteMap = new clsYML();
$objSiteMap->StartCreateYML();
echo "Done";
