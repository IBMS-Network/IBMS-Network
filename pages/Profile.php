<?php

namespace pages;

use classes\core\clsPage;
use classes\clsWebAuthorisation;
use classes\clsUser;
use classes\core\clsCommon;
use DateTime;
use classes\clsValidation;
use entities\UserSexTypes;

class Profile extends clsPage
{
    public function __construct()
    {
        parent::clsPage();
    }

    /**
     * Get rendered profile page
     * 
     * @return string
     */
    protected function getContent()
    {
        $this->parser->profileMenuAlias = 'profile';
        
        if(clsWebAuthorisation::getInstance()->isAuthorized()) {
            $this->parser->user =  clsWebAuthorisation::getInstance()->getUserSession();
            $this->parser->userSexTypes = UserSexTypes::getValues();
           
            //change user data
            if($this->post) {
                if($errors = $this->validate($this->post)) {
                    $this->parser->errorMessage = $errors;
                } else {
                    $this->post['birth_date'] = DateTime::createFromFormat('Y-n-j', implode('-', array($this->post['date3'], $this->post['date2'], $this->post['date']))); 
                    $this->post['id'] = $this->parser->user['id'];
                    $user = clsUser::getInstance()->updateUser($this->post);

                   //update user session
                    if(!empty($user)) {
                        clsWebAuthorisation::getInstance()->setUserSession($user);
                        $this->parser->user = clsWebAuthorisation::getInstance()->getUserSession();
                        $this->parser->message = clsCommon::getMessage('success_change_profile', 'Profile');
                    }
                }
            }
        } else {
            clsCommon::redirect302('Location: ' . '/');
        }
        
        return $this->parser->render('@main/pages/profile.html');
    }

    /**
     * Validate form fields
     * 
     * @param array $data
     * @return array
     */
    private function validate($data) {
        $errors = array();
        if(!clsValidation::requiredValidation($data['first_name'])) {
            $errors[] = clsCommon::getMessage('error_first_name_short', 'ErrorsValidation');
        }
        if(!clsValidation::requiredValidation($data['last_name'])) {
            $errors[] = clsCommon::getMessage('error_last_name_short', 'ErrorsValidation');
        }
        if(!clsValidation::nameStringValidation($data['first_name'])) {
            $errors[] = clsCommon::getMessage('error_incorrect_name',
                            'ErrorsValidation',
                            array('?'),
                            array(clsCommon::getMessage('field_first_name', 'ProfileFieldsNames')));
        }
        if(!clsValidation::requiredValidation($data['last_name'])) {
            $errors[] = clsCommon::getMessage('error_incorrect_name',
                            'ErrorsValidation',
                            array('?'),
                            array(clsCommon::getMessage('field_last_name', 'ProfileFieldsNames')));
        }
        if(empty($data['date']) || empty($data['date2']) || empty($data['date3'])) {
            $errors[] = clsCommon::getMessage('error_date_not_correct', 'ErrorsValidation');
        }
        if(empty($data['sex'])) {
            $errors[] = clsCommon::getMessage('error_sex_not_correct', 'ErrorsValidation');
        }
        if(!clsValidation::requiredValidation($data['phone'])) {
            $errors[] = clsCommon::getMessage('error_incorrect_name',
                    'ErrorsValidation',
                    array('?'),
                    array(clsCommon::getMessage('field_phone', 'CallbackFieldsNames')));
        } elseif(!clsValidation::phoneExtValidation($data['phone'])) {
            $errors[] = clsCommon::getMessage('error_phone',
                    'ErrorsValidation',
                    array('?'),
                    array(clsCommon::getMessage('field_phone', 'ProfileFieldsNames')));
        }
        if(!clsValidation::emailValidation($data['email'])) {
            $errors[] = clsCommon::getMessage('error_email',
                            'ErrorsValidation',
                            array('?'),
                            array(clsCommon::getMessage('field_email', 'ProfileFieldsNames')));
        }
        return empty($errors) ? false : $errors;
    }
}
