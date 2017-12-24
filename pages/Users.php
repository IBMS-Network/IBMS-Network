<?php

namespace pages;

use classes\clsUser;
use classes\clsValidation;
use classes\clsWebAuthorisation;
use classes\core\clsCommon;
use classes\core\clsPage;

class Users extends clsPage
{
    /**
     * @var clsUser;
     */
    private $clsUserObj;

    public function __construct()
    {
        parent::__construct();
        $this->clsUserObj = new clsUser;
        $this->parser->scripts = $this->scriptsManager->getHTML();
        $this->parser->styles = $this->stylesManager->getHTML();
    }

    public function register()
    {
        if ($this->post()) {
            /**
             * @todo Validators queue with errors' feedback on form
             */
            $firstName = clsValidation::requiredValidation($this->post('first_name'));
            $lastName = clsValidation::requiredValidation($this->post('last_name'));
            $email = clsValidation::requiredValidation(clsValidation::emailValidation($this->post('email')));
            $password = clsValidation::requiredValidation($this->post('password'));
            $address = $this->post('address');
            $phone = $this->post('phone');

            $userId = $this->clsUserObj->createUser($firstName, $lastName, $phone, $email, $password, $address);
            switch ($userId) {
                case clsUser::STATUS_USER_ALREADY_EXISTS:
                    $this->parser->error = 'User with this email already exists. Please type a new email.';
                    $this->parser->setVars($this->post());
                    break;
                case clsUser::STATUS_USER_DATA_INVALID:
                    $this->parser->error = 'User data is invalid. Please check it again before submit.';
                    $this->parser->setVars($this->post());
                    break;
                default:
                    clsCommon::redirect302('Location: ' . '/users/login');
            }
        }

        return $this->parser->render('@main/pages/users/register.html');
    }

    public function login()
    {
        var_dump(1511156);
        if (clsWebAuthorisation::getInstance()->isAuthorized()) {
            clsCommon::redirect302('Location: ' . '/');
        }

        if ($this->post()) {
            $email = clsValidation::requiredValidation(clsValidation::emailValidation($this->post('email')));
            $password = clsValidation::requiredValidation($this->post('password'));

            $authRes = clsWebAuthorisation::getInstance()->login($email, $password);
            if ($authRes) {
                clsCommon::redirect301('Location: /');
            } else {
                /**
                 * @todo show feedback on form
                 */
            }
        } else {
            $this->parser->user = clsWebAuthorisation::getInstance()->getUserSession();
        }

        return $this->parser->render('@main/pages/users/login.html');
    }

    public function logout()
    {
        clsWebAuthorisation::getInstance()->logout();
        clsCommon::redirect301('Location: /');
    }

}
