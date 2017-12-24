<?php

namespace pages;

use classes\core\clsPage;
use classes\clsWebAuthorisation;
use classes\core\clsCommon;
use classes\clsUsersRecovery;
use classes\clsUser;

class PasswordRecoveryComplete extends clsPage
{

    public function __construct()
    {
        parent::clsPage();
    }

    /**
     * Get rendered password recovery complete page
     * 
     * @return string
     */
    protected function getContent()
    {
        if(clsWebAuthorisation::getInstance()->isAuthorized()) {
            clsCommon::redirect302("Location: " . SERVER_URL_NAME . "/");
        }
        
        $info = clsUsersRecovery::getInstance()->getRecoveryInfo($this->get['hash']);
        if (!empty($this->get['hash']) && !empty($info)) {
            if (!empty($this->post)) {
                if (!empty($this->post['pass']) && !empty($this->post['pass2'])) {
                    if ($this->post['pass'] == $this->post['pass2']) {
                        $user = clsUser::getInstance()->getUserByEmail($info->getEmail());
                        if($user) {
                            $updUser = clsUser::getInstance()->updateUser(array('password' => $this->post['pass'], 'id' => $user->getId()));
                        }
                        if(!empty($updUser)) {
                           clsWebAuthorisation::getInstance()->setUserSession($updUser);
                           $this->parser->message = clsCommon::getMessage('success_change_password', 'Profile');
                           clsUsersRecovery::getInstance()->setRecoveryInfoStatus($info->getEmail(), $this->get['hash']);
                        }
                    } else {
                        $this->parser->errorMessage = clsCommon::getMessage('error_new_password_not_equal_old_one', 'ErrorsValidation');
                    }
                } else {
                    $this->parser->errorMessage = clsCommon::getMessage('error_fill_all_fields', 'ErrorsValidation');
                }
            }
        } else {
            clsCommon::redirect302("Location: " . SERVER_URL_NAME . "/");
        }

        return $this->parser->render('@main/pages/users/recovery_complete.html');
    }
}