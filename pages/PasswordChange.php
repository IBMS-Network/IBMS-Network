<?php

namespace pages;

use classes\core\clsPage;
use classes\clsUser;
use classes\clsWebAuthorisation;
use classes\core\clsCommon;

class PasswordChange extends clsPage
{

    public function __construct()
    {
        parent::clsPage();
    }

    /**
     * Get rendered password change page
     * 
     * @return string
     */
    protected function getContent()
    {
        $this->parser->profileMenuAlias = 'changepassword';
        
        if(!clsWebAuthorisation::getInstance()->isAuthorized()) {
            clsCommon::redirect302('Location: ' . '/');
        }
        
        $user = clsWebAuthorisation::getInstance()->getUserSession();
        
        //save new password
        if (!empty($this->post)) {
            if (!empty($this->post['pass']) && !empty($this->post['pass2']) && !empty($this->post['old_pass'])) {
                if(password_verify($this->post['old_pass'], $user['password'])) {
                    if ($this->post['pass'] == $this->post['pass2']) {
                        $updUser = clsUser::getInstance()->updateUser(array('password' => $this->post['pass'], 'id' => $user['id']));
                        if(!empty($updUser)) {
                           clsWebAuthorisation::getInstance()->setUserSession($updUser);
                           $this->parser->message = clsCommon::getMessage('success_change_password', 'Profile');
                        }
                    } else {
                        $this->parser->errorMessage = clsCommon::getMessage('error_new_password_not_equal_old_one', 'ErrorsValidation');
                    }
                } else {
                    $this->parser->errorMessage = clsCommon::getMessage('error_old_password_not_correct', 'ErrorsValidation');
                }
            } else {
                $this->parser->errorMessage = clsCommon::getMessage('error_fill_all_fields', 'ErrorsValidation');
            }
        }
        
        return $this->parser->render('@main/pages/password_change.html');
    }

}