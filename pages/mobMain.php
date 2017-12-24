<?php

namespace pages;

use engine\modules\mobile\clsMobController;

class mobMain extends clsMobController
{

    /**
     * Inner variable to hold own object of a class
     * @var object $instance - object of the mobMain
     */
    private static $instance = null;

    /**
     * getInstance function create or return alreadty exists object of this class
     *
     * @return object $instance - object of this class
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new mobMain();
        }
        return self::$instance;
    }

    /**
     * Get start page
     *
     * @return array
     */
    public function actionIndex($request = array())
    {
        $return = array();
        return $return;
    }

}