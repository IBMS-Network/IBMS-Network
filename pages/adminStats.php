<?php

namespace pages;

use engine\modules\admin\clsAdminController;

class adminStats extends clsAdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->parser->is_stats_menu = true; //set active Stats in left menu
    }

    /**
     * Inner variable to hold own object of a class
     * @var object $instance - object of the mobMain
     */
    private static $instance = null;

    private $objSession = "";

    /**
     * getInstance function create or return alreadty exists object of this class
     *
     * @return object $instance - object of this class
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new adminStats();
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
        return $this->parser->render('@main/pages/admin_stats.html');
    }

}