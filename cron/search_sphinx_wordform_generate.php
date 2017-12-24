<?php
// cd /srv/www/ipointer/cron/; /usr/bin/php search_sphinx_indexer_all_rotate.php console

define('CRON_MODE', 1);

ini_set('date.timezone', 'Europe/Moscow');

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
require_once( realpath(dirname(dirname(__FILE__))) . "/config/config.php" );

try {

    require_once( COMMON_CLS_PATH . "clsCommon.php" );
    require_once( COMMON_CLS_PATH . "clsContent.php" );
    require_once( COMMON_CLS_PATH . "clsDB.php" );
    require_once( COMMON_CLS_PATH . "clsCore.php" );
    require_once( COMMON_CLS_PATH . "clsError.php" );
	
    require_once( CLS_PATH . "clsSynonyms.php" );
    
    require_once( DICTIONARY_PATH . "errtext.php" );

    
    set_error_handler(array('clsCommon', 'errorHandler'));
    
	// get list wordform
	$allWordFormsList = clsSynonyms::getInstance()->getWordFormsList();
	
	// write wordform to file
	$fp = fopen( SEARCH_SPHINX_DATA_DIR . 'wordforms.txt', 'w+');
	foreach ($allWordFormsList As $wordForm){
		$str = trim(mb_strtolower($wordForm['wordform'], 'UTF-8')) . ' > ' . trim(mb_strtolower($wordForm['word'], 'UTF-8')) . "\n";
		fwrite($fp, $str);
	}
	fclose($fp);
	
	
} catch (Exception $e) {
    if (USE_DEBUG) {
        clsCommon::debugMessage($e->getMessage(), "cron:Exeption");
        exit();
    } else {
        exit("System is down. Please contact to support.");
    }
}
