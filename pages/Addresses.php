<?php

namespace pages;

use classes\core\clsPage;
use classes\clsWebAuthorisation;
use classes\clsUsersAddresses;
use classes\core\clsCommon;

class Addresses extends clsPage
{

    public function __construct()
    {
        parent::clsPage();
    }

    /**
     * Get rendered addresses page
     * 
     * @return string
     */
    protected function getContent()
    {
        //get identity for menu
        $this->parser->profileMenuAlias = 'addresses';
        
        if (clsWebAuthorisation::getInstance()->isAuthorized()) {
            $user = clsWebAuthorisation::getInstance()->getUserSession();
            
            //delete user address
            if($this->get['action'] == 'delete' && !empty($this->get['id'])){
                if(clsUsersAddresses::getInstance()->deleteUserAddress($this->get['id'], $user['id']))
                    clsCommon::redirect301('Location: /addresses');
            }
            
            //add or edit user address
            if(!empty($this->post)) {
                $this->post['user_id'] = $user['id'];
                clsUsersAddresses::getInstance()->updateAddress($this->post);
            }
            
            //get user addresses for template
            $this->parser->addresses = clsUsersAddresses::getInstance()->getUserAddresses($user['id']);
        } else {
            clsCommon::redirect302('Location: ' . '/');
        }

        return $this->parser->render('@main/pages/addresses.html');
    }

}
