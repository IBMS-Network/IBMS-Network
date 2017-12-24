<?php

require_once(realpath(dirname(__FILE__) . "/..") . "/config/config.php" );
require_once (ENGINE_PATH . "sys_index.php");
require_once (COMMON_CLS_PATH . "clsCommon.php");
require_once (SERVER_ROOT . "bootstrap.php");
require_once( PAGE_PATH . "SearchSphinxHelper.class.php" );

//	$objCore = new clsCore();
//	$objCore->runApp();
// params
$query = (isset($_GET['query']) && !empty($_GET['query'])) ? trim($_GET['query']) : '';

$search = new SearchSphinxHelper();

// result array
$result = $search->search($query, 1, DEF_LIST_LIMIT);

echo json_encode($result['result']);
