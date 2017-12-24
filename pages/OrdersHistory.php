<?php

namespace pages;

use classes\core\clsPage;
use classes\clsWebAuthorisation;
use classes\core\clsCommon;
use classes\clsOrder;

class OrdersHistory extends clsPage
{

    public function __construct()
    {
        parent::clsPage();
    }

    /**
     * Get rendered user orders history page
     * 
     * @return string
     */
    protected function getContent()
    {
        if (clsWebAuthorisation::getInstance()->isAuthorized()) {
            $user = clsWebAuthorisation::getInstance()->getUserSession();

            //prepare DB filter
            $limit = null;
            $to = !empty($this->get['to']) ? $this->get['to'] : '';
            switch($to) {
                case 'month':
                    $limit = date('Y-m-01');
                    break;
                case 'year':
                    $limit = date('Y-01-01');
                    break;
                case 'quarter':
                    $limit = date('Y-m-d', strtotime('-3 month'));
                    break;
                default:
                    if (date('w') == 1) {
                        $limit = date('Y-m-d', strtotime('Monday'));
                    } else {
                        $limit = date('Y-m-d', strtotime('last Monday'));
                    }
                    break;
            }
            $this->parser->profileMenuAlias = $to;
            
            //get user orders using filter
            $this->parser->orders = clsOrder::getInstance()->getOrdersByUserId((int)$user['id'], $limit);
            $this->parser->deliveryCost = DELIVERY_COST;
        } else {
            clsCommon::redirect302('Location: ' . '/');
        }

        return $this->parser->render('@main/pages/orders_history.html');
    }

}
