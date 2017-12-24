<?php

use engine\clsSysCommon;

// init classes modules
$modules_names = array_merge(array('default'), $modules_names);

//$common_cls_path_classes = clsSysCommon::initUseModule($modules_names);
/*
// add default core classes
if (!empty($common_cls_path_classes['default'])){
    // init them
    foreach( $common_cls_path_classes['default'] as $value ){
        clsSysCommon::autoLoaderClass( COMMON_SYS_CLS_PATH, $value );
    }
    unset($common_cls_path_classes['default']);
}

// add include modules core classes
if (!empty($common_cls_path_classes)){
    // init them
    foreach( $common_cls_path_classes as $module => $classes ){
        $module_common_sys_path = "MODULE_" . strtoupper($module) . "_COMMON_SYS_CLS_PATH";  
        if (defined($module_common_sys_path)){
            foreach( $classes as $value ){
                clsSysCommon::autoLoaderClass( constant($module_common_sys_path), $value );
            }
        }
    }
}

// add framework classes
$framework_cls_path_classes = array( "clsSysEmail.php", "clsSysSession.php", "clsSysAuthorisation.php", "clsSysStatic.php", "clsSysValidation.php", "clsSysAcl.php", "clsSysServices.php", 'clsSysScripts.php', 'clsSysStyles.php' );

// init them
foreach( $framework_cls_path_classes as $value ){
    clsSysCommon::autoLoaderClass( CLS_SYS_PATH, $value );
}
*/
// add default dictionary

/**
 * @todo what is it?
 */
clsSysCommon::autoLoaderClass( DICTIONARY_SYS_PATH, "errtext.php" );
