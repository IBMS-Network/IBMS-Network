<?php

namespace pages;

use engine\modules\admin\clsAdminController;

class adminMain extends clsAdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->parser->is_main_menu = true; //set active Main in left menu

    }

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
            self::$instance = new adminMain();
        }
        return self::$instance;
    }

    /**
     * Get start page
     *
     * @return array
     */
    public function actionIndex()
    {
        return $this->parser->render('@main/pages/admin_index.html');
    }

}