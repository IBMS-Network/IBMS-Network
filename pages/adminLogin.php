<?php

namespace pages;

use classes\clsAdminAuthorisation;
use engine\modules\admin\clsAdminController;

class adminLogin extends clsAdminController
{

    /**
     * Inner variable to hold own object of a class
     *
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
            self::$instance = new adminLogin();
        }

        return self::$instance;
    }

    public function actionIndex()
    {

        if (!empty($_POST)
            && $_POST["method"] == "admin_login"
            && !empty($_POST['login'])
            && !empty($_POST['password'])
        ) {
            $authRes = clsAdminAuthorisation::getInstance()->login($_POST['login'], $_POST['password']);
        }

        $authErrors = false;
        if ($authRes === false) {
            $authErrors = $this->error->getErrorMessage("INCORRECT_INPUT_DATA");
        }

        $errors = array();
        if ($authErrors) {
            $errors['is_auth_error'] = true;
            $errors['message_error'] = $authErrors;
        } else {
            $errors['is_auth_error'] = false;
        }
        $this->parser->title = 'Administration login';

        return $this->parser->render('@main/pages/admin_login.html', $errors);
    }

}