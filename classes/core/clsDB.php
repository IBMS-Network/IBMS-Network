<?php

namespace classes\core;

use engine\clsSysDB;

class clsDB extends clsSysDB
{
    
}

function myError($datatype, $func_type = "", $errnum = "", $errmsg = "", $sql = "", $inputarr = "", $class = "")
{
    $error = "MYSQL ERROR : " . $errmsg . "<br /> SQL : " . $sql . "<br /> FILE : " . __FILE__ . " LINE : " . __LINE__;
    if (USE_ERROR_LOG) {
        clsCommon::Log($error, 3, "mysql_log.log");
    };
    if (USE_DEBUG) {
        if (defined('API_MODE')) {
            throw new \Exception($error, API_ERROR_CODE_ERROR);
        } else {
            die($error);
        }
    } else {

        $error = 'Error on Connect';
        //clsCommon::redirect302("Location: /Warning/?err_num=".DEF_SQL_QUERY_ERR);
        if (defined('API_MODE')) {
            throw new \Exception($error, API_ERROR_CODE_ERROR);
        } else {
            die($error);
        }
    }
}
