<?php

namespace pages;

use classes\core\clsCommon;
use engine\clsSysAuthorisation;
use engine\modules\admin\clsAdminController;

class adminLogout extends clsAdminController
{

    /**
     * Inner variable to hold own object of a class
     * @var object $instance - object of the mobMain
     */
    private static $instance = null;

    public function actionIndex()
    {
        clsSysAuthorisation::getInstance()->logoutAdmin();
        clsCommon::redirect301('Location: /admin/login/');
    }

}