<?php

namespace pages;

use classes\core\clsPage;
use classes\clsWebAuthorisation;
use classes\core\clsCommon;
use classes\clsValidation;
use classes\clsUsersRecovery;
use classes\clsEmailTemps;
use classes\clsEmail;

class PasswordRecovery extends clsPage
{

    public function __construct()
    {
        parent::clsPage();
    }

    /**
     * Get rendered password recovery page
     * 
     * @return string
     */
    protected function getContent()
    {
        if(clsWebAuthorisation::getInstance()->isAuthorized()) {
            clsCommon::redirect302("Location: " . SERVER_URL_NAME . "/");
        }
        
        if (!empty($this->post)) {
            if (clsValidation::emailValidation($this->post['email'])) {
                if (!clsValidation::uniqueEmailValidation($this->post['email'])) {
                    $hash = $this->generatePassword($this->post['email']);
                    if (clsUsersRecovery::getInstance()->setRecoveryInfo($this->post['email'], $hash)) {
                        $template = clsEmailTemps::getInstance()->getEmailtempById(3);
                        if(!empty($template)) {
                            $params = array(
                                        '{{LINK}}' => SERVER_URL_NAME . '/password_recovery_complete/' . $hash . '/',
                            );
                            clsEmail::getInstance()->send($template->getSubject(), $this->post['email'], $template->getValue(), $params);

                            $this->parser->message = clsCommon::getMessage('message_sent', 'Feedback');
                        }
                        
                        $this->parser->message = clsCommon::getMessage('success_recovery_password', 'Profile') . $this->post['email'];
                    } else {
                        $this->parser->errorMessage = clsCommon::getMessage('system_error', 'Errors');
                    }
                } else {
                    $this->parser->errorMessage = clsCommon::getMessage('error_email_not_used', 'ErrorsValidation');
                }
            } else {
                $this->parser->errorMessage = clsCommon::getMessage('error_email_short', 'ErrorsValidation');
            }
        }

        return $this->parser->render('@main/pages/users/recovery.html');
    }

    private function generatePassword($email)
    {
        $res = md5($email . uniqid(rand(), true));

        return $res;
    }

}