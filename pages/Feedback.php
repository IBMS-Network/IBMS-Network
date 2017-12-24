<?php

namespace pages;

use classes\core\clsPage;
use classes\clsEmailTemps;
use classes\clsEmail;
use classes\clsDeliveries;
use classes\core\clsCommon;
use classes\clsValidation;

class Feedback extends clsPage
{

    public function __construct()
    {
        parent::clsPage();
        $this->parser->menuAlias = 'feedback';
    }

    /**
     * Get rendered feedback page
     * 
     * @return string
     */
    protected function getContent()
    {
        //send feedback email
        if($this->post) {
            $this->parser->incomming = $this->post;
            if($errors = $this->validate($this->post)) {
                $this->parser->errorMessage = $errors;
            } else {
                $template = clsEmailTemps::getInstance()->getEmailtempById(FEEDBACK_MAIL_ID);
                if(!empty($template)) {
                    $params = array(
                                '{{USER}}' => $this->post['name'],
                                '{{EMAIL}}' => $this->post['email'],
                                '{{TEXT}}'  => $this->post['text']
                    );
                    clsEmail::getInstance()->send($template->getSubject(), $template->getEmail(), $template->getValue(), $params);

                    $this->parser->message = clsCommon::getMessage('message_sent', 'Feedback');
                }
            }
        }
            
        
        //get deliveries addresses for map
        $this->parser->deliveries = clsDeliveries::getInstance()->getDeliveries();
        
        return $this->parser->render('@main/pages/contacts.html');
    }

    /**
     * Validate form fields
     * 
     * @param array $data
     * @return array
     */
    private function validate($data) {
        $errors = array();
        if(!clsValidation::requiredValidation($data['name'])) {
            $errors[] = clsCommon::getMessage('error_first_name_short', 'ErrorsValidation');
        } elseif(!clsValidation::nameStringValidation($data['name'])) {
            $errors[] = clsCommon::getMessage('error_incorrect_name',
                            'ErrorsValidation',
                            array('?'),
                            array(clsCommon::getMessage('field_name', 'FeedbackFieldsNames')));
        }
        if(!clsValidation::emailValidation($data['email'])) {
            $errors[] = clsCommon::getMessage('error_email',
                            'ErrorsValidation',
                            array('?'),
                            array(clsCommon::getMessage('field_email', 'FeedbackFieldsNames')));
        }
        if(!clsValidation::requiredValidation($data['text'])) {
            $errors[] = clsCommon::getMessage('error_field_must_contain',
                            'ErrorsValidation',
                            array('?'),
                            array(clsCommon::getMessage('field_text', 'FeedbackFieldsNames')));
            
        }
        return empty($errors) ? false : $errors;
    }
}
