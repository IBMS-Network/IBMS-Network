<?php
namespace pages;

use classes\clsUser;
use classes\clsValidation;
use classes\core\clsCommon;
use classes\core\clsPage;

class Registration extends clsPage
{

    public function __construct()
    {
        parent::clsPage();
    }

    protected function getContent()
    {
        if ($this->post()) {
            /**
             * @todo Validators queue with errors' feedback on form
             */
            $firstName = clsValidation::requiredValidation($this->post('first_name'));
            $lastName = clsValidation::requiredValidation($this->post('last_name'));
            $email = clsValidation::requiredValidation(clsValidation::emailValidation($this->post('email')));
            $password = clsValidation::requiredValidation($this->post('password'));

            $userId = clsUser::getInstance()->createUser($firstName, $lastName, $email, $password, new \DateTime(), 1);
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
                    clsCommon::redirect302('Location: ' . '/profile');
            }
        }

        return $this->parser->render('@main/pages/users/register.html');
    }

}
