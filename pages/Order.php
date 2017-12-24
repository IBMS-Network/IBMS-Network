<?php
namespace pages;

use classes\clsOrder;
use classes\clsSession;
use classes\clsUsersAddresses;
use classes\clsWebAuthorisation as Auth;
use classes\core\clsCommon;
use classes\core\clsPage;
use engine\modules\catalog\clsProducts;
use DateTime;
use entities\DeliveryHoursTypes;
use classes\clsDeliveries;
use entities\OrderStatusTypes;
use classes\clsEmailTemps;
use classes\clsEmail;

class Order extends clsPage
{

    public function __construct()
    {
        parent::clsPage();
    }

    /**
     * Get rendered order page
     * 
     * @return string
     */
    protected function getContent()
    {
        if(clsSession::getInstance()->isCartEmpty()) {
            clsCommon::redirect301('Location: /cart/');
        }
        
        $this->parser->deliveries = clsDeliveries::getInstance()->getDeliveries();
        
        //get products from cart
        $cart = clsSession::getInstance()->getCart();
        $ids = array_keys($cart);
        $products = clsProducts::getInstance()->getProductsByIds($ids, true);

        //calculate sum
        if(!empty($products)) {
            $sum = 0;
            foreach($products as $key => $product) {
                $products[$key]->count = $cart[$product->getId()]['count'];
                $sum += ($cart[$product->getId()]['count'] * ($product->getPrice2() > 0 ? $product->getPrice2() : $product->getPrice()));
            }
        }
        
        $user = Auth::getInstance()->isAuthorized() ? Auth::getInstance()->getUserSession() : NULL;
        if ($user) {
            $this->parser->addresses = clsUsersAddresses::getInstance()->getUserAddresses($user['id']);

            $this->parser->userName = $user['firstName'] . ' ' . $user['lastName'];
            $this->parser->userEmail = $user['email'];
            $this->parser->userPhone = $user['phone'];
        }
        
        $this->parser->notRegUser = clsSession::getInstance()->getParam('unreg_user_info');
        //create order
        if (!empty($this->post)) {
            if(!($user || clsSession::getInstance()->getParam('unreg_user_info'))) {
                $userName = isset($this->post['userName']) ? $this->post['userName'] : '';
                $userName .= isset($this->post['userSecondName']) ? (' ' . $this->post['userSecondName']) : '';
                clsSession::getInstance()->setParam('userName', $userName,'unreg_user_info');
                clsSession::getInstance()->setParam('userPhone', (isset($this->post['userPhone']) ? $this->post['userPhone'] : ''),'unreg_user_info');
                clsSession::getInstance()->setParam('userEmail', (isset($this->post['userEmail']) ? $this->post['userEmail'] : ''),'unreg_user_info');
                
                $this->parser->notRegUser = clsSession::getInstance()->getParam('unreg_user_info');
            } else {
                $userId = $user ? (int)$user['id'] : 0;
                $address = '';
                $deliveryDate = null;
                $deliveryHours = null;
                $deliveryAddress = null;
                $userName = null;
                $userPhone = null;
                $userEmail = null;
                if(empty($this->post['pickup'])) {
                    $address = $this->post['address'] ? $this->post['address'] : '';
                    //add address if not exists
                    if(!$this->parser->addresses) {
                        clsUsersAddresses::getInstance()->updateAddress(array('user_id' => $userId, 'address' => $address));
                    }

                    $deliveryDate = (!empty($this->post['delivery_date']) && (strtotime($this->post['delivery_date']) !== false))
                            ? new DateTime($this->post['delivery_date']) : NULL;
                    $deliveryHours = (!empty($this->post['delivery_hours'])) ? $this->post['delivery_hours'] : NULL;

                    $sum += DELIVERY_COST;
                } else {
                    $deliveryAddress = !empty($this->post['delivery-address']) ? (int)$this->post['delivery-address'] : NULL;
                }
                $comments = $this->post['comments'] ? $this->post['comments'] : '';
                $payment = isset($this->post['payment']) ? $this->post['payment'] : 'nal';
                if($userId == 0) {
                    $userName = clsSession::getInstance()->getParam('userName', 'unreg_user_info');
                    $userPhone = clsSession::getInstance()->getParam('userPhone', 'unreg_user_info');
                    $userEmail = clsSession::getInstance()->getParam('userEmail', 'unreg_user_info');
                }

                // add order
                $id = clsOrder::getInstance()->addOrder($userId, $address, $comments, $deliveryDate, $payment, $products,
                                                    $deliveryHours, $deliveryAddress, OrderStatusTypes::getValueByConstant(OrderStatusTypes::STATE_WAITING),
                                                    $userName, $userPhone, $userEmail);

                clsSession::getInstance()->clearParam('unreg_user_info');
                clsSession::getInstance()->clearCart();
                clsSession::getInstance()->setOrderId($id);

                //send email
                $orderProducts = '';
                $orderDetails = '';
                foreach ($products as $product) {
                    if(!empty($product)) {
                        $price = $product->getPrice2() > 0 ? $product->getPrice2() : $product->getPrice();
                        $orderProducts .= $product->getName() . ' ' . '(ID ' . $product->getId() . ')';
                        $orderProducts .= '  ' . $price . ' руб. * ' . $product->count . ' шт.';
                        $orderProducts .= ' = ' . $price * $product->count . ' руб.\n';
                    }
                }
                if(empty($this->post['pickup'])) {
                    $orderDetails .= 'Адрес доставки: ' . $address . '\n';
                    $orderDetails .= 'Стоимость доставки: ' . DELIVERY_COST . 'руб.';
                } else {
                    $orderDetails .= 'Самовывоз из ' . clsDeliveries::getInstance()->getDeliveryById($deliveryAddress)->getValue();
                }
                $params = array(
                            '{{USERNAME}}' => $userId > 0 ? ($user['firstName'] . ' ' . $user['lastName']) : $userName,
                            '{{USER_PHONE}}' => $userId > 0 ? $user['phone'] : $userPhone,
                            '{{SUM}}' => $sum,
                            '{{ORDER_ID}}'  => $id,
                            '{{ORDER_PRODUCTS}}'  => $orderProducts,
                            '{{ORDER_DETAILS}}'  => $orderDetails
                );
                $template = clsEmailTemps::getInstance()->getEmailtempById(CUSTOMER_ORDER_MAIL_ID);
                if(!empty($template) && !empty($id)) {
                    clsEmail::getInstance()->send($template->getSubject(),
                            !empty($user) ? ($user['email']) : $userEmail,
                            $template->getValue(),
                            $params);
                }
                $template = clsEmailTemps::getInstance()->getEmailtempById(MANAGER_ORDER_MAIL_ID);
                if(!empty($template) && !empty($id)) {
                    clsEmail::getInstance()->send($template->getSubject(),
                            $template->getEmail(),
                            $template->getValue(),
                            $params);
                }
                
                if($payment == 2) {
                    $url = $this->prepareRobocassa($id, $sum);
//                } elseif($payment == 3) {
//                    $url = $this->prepareYandexMoney($id, $sum);
                } else {
                    $url = '/checkout/';
                }
                clsCommon::redirect301('Location: ' . $url);
            }
        }


        $this->parser->products = $products;
        $this->parser->sum = $sum;
        $this->parser->path = SERVER_URL_NAME;
        $this->parser->deliveryCost = DELIVERY_COST;
        $this->parser->deliveryFirstLimit = DELIVERY_FIRST_LIMIT;
        $this->parser->deliverySecondLimit = DELIVERY_SECOND_LIMIT;
        $this->parser->deliveryHours = DeliveryHoursTypes::getValues();

        return $this->parser->render('@main/pages/order.html');
    }
    
    /**
     * Prepare robocassa url
     * 
     * @param integer $id
     * @param float $sum
     * @return string
     */
    private function prepareRobocassa($id, $sum) {
        $url = '';
        if(!empty($id) && !empty($sum)) {
            $sum = number_format($sum, 2, '.', '');
            $id = clsCommon::isInt($id);
            $signature = ROBOCASSA_MERCHANT_LOGIN . ':'
                            . $sum . ':' . $id . ':'
                            . ROBOCASSA_PASSWORD_1;
            $params = array('OutSum=' . $sum,
                            'InvId=' . $id,
                            'MrchLogin=' . ROBOCASSA_MERCHANT_LOGIN,
                            'Desc=' . 'Покупка в магазине.',
                            'SignatureValue=' . md5($signature));
            
            $url = ROBOCASSA_URL . '?' . join('&', $params);
        }
        
        return $url;
    }
}
