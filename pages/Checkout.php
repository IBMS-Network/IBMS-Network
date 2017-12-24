<?php
namespace pages;

use classes\clsSession;
use classes\core\clsPage;
use classes\clsOrder;
use classes\clsUsersAddresses;

class Checkout extends clsPage
{

    public function __construct()
    {
        parent::clsPage();
    }

    /**
     * Get rendered checkout page
     * 
     * @return string
     */
    protected function getContent()
    {
        $this->parser->orderId = clsSession::getInstance()->getOrderId();
        $this->parser->order = clsOrder::getInstance()->getOrderById((int)$this->parser->orderId);
        $this->parser->path = SERVER_URL_NAME;
        $this->parser->deliveryCost = DELIVERY_COST;

        if(!empty($this->post)) {
            if(!empty($this->post['OutSum']) && !empty($this->post['InvId']) 
                    && $this->post['InvId'] == $this->parser->orderId && !empty($this->post['SignatureValue'])){
                
                $signature = md5($this->post['OutSum'] . ':' . $this->post['InvId'] . ':' . ROBOCASSA_PASSWORD_1);
                if($signature == $this->post['SignatureValue']) {
                    $this->parser->paySum = $this->post['OutSum'];
                }
            }
        }
        

        return $this->parser->render('@main/pages/order_success.html');
    }

}
