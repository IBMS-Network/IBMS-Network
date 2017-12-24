<?php
    session_start();
    require_once(realpath(dirname(__FILE__)."/..")."/project/config/config.php" );
    require_once( COMMON_CLS_PATH . 'clsCommon.php' );
    require_once( COMMON_CLS_PATH . 'clsContent.php' );
    require_once( COMMON_CLS_PATH . 'clsDB.php' );
    require_once( COMMON_CLS_PATH . 'clsPage.php' );
    require_once( COMMON_CLS_PATH . 'clsBlocks.php' );
    require_once( COMMON_CLS_PATH . 'clsCore.php' );
    require_once( COMMON_CLS_PATH . 'clsParser.php' );
    require_once( COMMON_CLS_PATH . 'clsError.php' );
    require_once( COMMON_CLS_PATH . "clsAjax.php" );
    require_once( COMMON_CLS_PATH . "clsUser.php" );
    require_once( CLS_PATH . 'clsSession.php' );
    require_once( CLS_PATH . "clsAuthorisation.php" );
    
    $auth = new clsAuthorisation();
    return $auth->logout();
