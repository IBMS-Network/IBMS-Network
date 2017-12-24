<?php
namespace pages;

use classes\core\clsCommon;
use classes\core\clsPage;
use classes\clsOrder;
use entities\OrderStatusTypes;

class Roboresult extends clsPage
{

    public function __construct()
    {
        parent::clsPage();
    }

    /**
     * Get response for robocassa
     * 
     * @return string
     */
    protected function getContent()
    {
        $body = 'Error';
        if (!empty($this->post)) {
            if(!empty($this->post['OutSum']) && !empty($this->post['InvId'])
                    && !empty($this->post['SignatureValue'])){
                
                $signature = strtoupper(md5($this->post['OutSum'] . ':' . $this->post['InvId'] . ':' . ROBOCASSA_PASSWORD_2));
				if($signature == $this->post['SignatureValue']) {
                    $result = clsOrder::getInstance()->updateOrder(array(
                                                                    'id' => (int)$this->post['InvId'],
                                                                    'status' => OrderStatusTypes::getValueByConstant(OrderStatusTypes::STATE_PAYOFF)));
                    if($result) {
                        //send email
                        $template = clsEmailTemps::getInstance()->getEmailtempById(PAYMENT_MAIL_ID);
                        $order = clsOrder::getInstance()->getOrderById((int)$this->post['InvId']);
                        if(!empty($template) && !empty($order)) {
                            $params = array(
                                        '{{USERNAME}}' => !empty($order->getUser()) ? ($order->getUser()->getFirstName() . ' ' . $order->getUser()->getLastName()) : $order->getUserName(),
                                        '{{SUM}}' => $this->post['OutSum'],
                                        '{{ORDER_ID}}'  => $this->post['InvId']
                            );
                            clsEmail::getInstance()->send($template->getSubject(),
                                    !empty($order->getUser()) ? ($order->getUser()->getEmail()) : $order->getUserEmail(),
                                    $template->getValue(),
                                    $params);
                        }
                        
                        $body = 'OK' . $this->post['InvId'];
                    }
                }
            }
            
            return $body;
        } else {
            clsCommon::redirect404();
        }
    }
}
