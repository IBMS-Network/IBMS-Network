<?php
namespace pages;

use classes\core\clsCommon;
use classes\core\clsPage;
use classes\clsOrder;
use entities\OrderStatusTypes;

class Robofail extends clsPage
{

    public function __construct()
    {
        parent::clsPage();
    }

    /**
     * Get rendered robocassa fail page
     * 
     * @return string
     */
    protected function getContent()
    {
        if (!empty($this->post)) {
            clsOrder::getInstance()->updateOrder(array(
                                                    'id' => (int)$this->post['InvId'],
                                                    'status' => OrderStatusTypes::getValueByConstant(OrderStatusTypes::STATE_REJECTED)));
            $this->parser->orderId = (int)$this->post['InvId'];
            $this->parser->sum = (float)$this->post['OutSum'];
            
            return $this->parser->render('@main/pages/robofail.html');
        } else {
            clsCommon::redirect404();
        }
    }
}
