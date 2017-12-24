<?php

session_start();
require_once(realpath(dirname(__FILE__) . "/..") . "/config/config.php" );
require_once( COMMON_CLS_PATH . 'clsCommon.php' );
require_once( COMMON_CLS_PATH . 'clsContent.php' );
require_once( COMMON_CLS_PATH . 'clsDB.php' );
require_once( COMMON_CLS_PATH . 'clsPage.php' );
require_once( COMMON_CLS_PATH . 'clsBlocks.php' );
require_once( COMMON_CLS_PATH . 'clsCore.php' );
require_once( COMMON_CLS_PATH . 'clsParser.php' );
require_once( COMMON_CLS_PATH . 'clsError.php' );
require_once( COMMON_CLS_PATH . "clsAjax.php" );
require_once( CLS_PATH . "clsUser.php" );
require_once( CLS_PATH . "clsOrder.php" );
require_once( CLS_PATH . "clsEmail.php" );
require_once( CLS_PATH . 'clsSession.php' );
require_once( CLS_PATH . "clsAuthorisation.php" );
require_once( CLS_PATH . "clsValidation.php" );
require_once( CLS_PATH . "clsClient.php" );
require_once( CLS_PATH . "clsCompany.php" );
require_once( CLS_PATH . "clsEvent.php" );

$allcat = array('test');
$allsub = array();
$categories = array();
$subcategories = array();
$treeCount = 0;
$newRes = array();
$insertVals = array();
$updateVals = array();

$db = DB::getInstance();

$sql = "SELECT id, name
        FROM categories";
$result = $db->GetAll($sql);

if (!empty($result)) {
    foreach($result as $k => $v) {
        $newRes[$v['id']] = $v['name'];
    }
}

if (($handle = fopen("Catalog.Data.Ipointer.From.Client.04.12.2012.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        $num = count($data);
        if($data[0] != '') {
            $key = array_search($data[0], $newRes);
            if(!$key) {
                $sql = "INSERT
                        INTO categories(outer_id, parent_id, category_type_id, name, description,
                                weight, icon, secondary, status)
                        VALUES (1, 0, 1, ?, ?, 1, ?, 0, 1)";
                $sqlArr = array($data[0], '', '');
                $res = $db->Execute($sql, $sqlArr);
                if($res) {
                    $key = $db->Insert_ID();
                }
            }
        }
        if($key) {
            for ($c=1; $c < $num; $c++) {
                $key2 = array_search($data[$c], $newRes);
                if(!$key2) {
                    $insertVals[] = '(1, ' . $key . ', 1, "' . $data[$c] . '", "", 1, "", 0, 1)';
                } else {
                    $updateVals[$key2] = $key;
                }
            }
        }
    }
}
fclose($handle);
if(!empty($insertVals)) {
    $sql = "INSERT
            INTO categories(outer_id, parent_id, category_type_id, name, description,
                    weight, icon, secondary, status)
            VALUES " . implode(',', $insertVals);
    $res = $db->Execute($sql);
}
if(!empty($updateVals)) {
    foreach($updateVals as $k => $v) {
        $sql = "UPDATE categories
            SET parent_id = ?
            WHERE id = ?";
        $sqlArr = array($v, $k);
        $res = $db->Execute($sql);
    }
}
    
    
//    $sql = "SELECT id, name
//            FROM categories
//            WHERE name IN ('" . implode('\',\'', $allcat) . "')";
////    var_dump($sql);
//    $result = $this->db->GetAll($sql);
//    var_dump($result);
