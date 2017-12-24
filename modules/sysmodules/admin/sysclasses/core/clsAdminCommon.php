<?php
namespace engine\modules\admin;

use engine\clsSysCommon;

class clsAdminCommon extends clsSysCommon
{
    private static $is_add_errors = false;

    private static $_notes = array();

    public static function getAdminMessage( $message , $blockname = ADMIN_ERROR_BLOCK, $replacement = array())
    {
        $search = $repl = array();
        self::setAdminMessages();
        if(!empty($replacement) && is_array($replacement)) {
            $search = array_keys($replacement);
            $repl = array_values($replacement);
        }
        return !empty($message) ? clsSysCommon::getMessage($message, $blockname, $search, $repl) : clsSysCommon::getMessage() ;
    }

    private static function setAdminMessages () {
        if(self::$_notes == array()) {
            $sys_ini_info = array('path' => MODULE_ADMIN_DICTIONARY_PATH, 'name' => 'notes.ini');
            $project_ini_info = $sys_ini_info;
            self::$_notes = self::getCommonIniFiles($sys_ini_info, $project_ini_info, false);
        }
        clsSysCommon::getDictionary();
        self::setMessages(self::$_notes);
    }
}