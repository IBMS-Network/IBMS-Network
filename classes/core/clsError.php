<?php

namespace classes\core;

use engine\clsSysError;

class clsError extends clsSysError
{

    protected static $instance = NULL;

    public static function getInstance()
    {

        if (self::$instance == NULL) {
            self::$instance = new clsError ();
        }
        return self::$instance;
    }

}
