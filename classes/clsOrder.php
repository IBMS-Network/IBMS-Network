<?php

namespace classes;

use classes\core\clsDB;
use entities\Order;
use classes\clsSession;
use entities\OrdersProducts;
use engine\modules\catalog\clsProducts;
use classes\core\clsCommon;
use entities\Product as ProjectProduct;
use entities\OrdersStatuses;

class clsOrder
{

    static private $instance = null;
    private $db = "";

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new clsOrder();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->em = clsDB::getInstance();
    }

    /**
     * Create order
     * @param integer $userId
     * user id
     * @param string $address
     * order address
     * @param string $comments
     * order comment
     * @param \Datetime $deliveryDate
     * order delivery date
     * @param string $payment
     * order payment type
     * @param array $products
     * array of product id in order
     * @param integer $deliveryHours
     * DeliveryHoursTypes
     * @param string $deliveryAddress
     * order delivery address
     * @param integer $status
     * order status
     * @param string $userName
     * order user name
     * @param string $userPhone
     * order user phone
     * @param string $userEmail
     * order user email
     * @return int
     */
    public function addOrder(
        $userId,
        $address,
        $comments,
        $deliveryDate,
        $payment,
        $products,
        $deliveryHours,
        $deliveryAddress,
        $status,
        $userName = null,
        $userPhone = null,
        $userEmail
    ) {
        $order = new Order();
        /** @var \entities\User $user */
        $user = $this->em->getRepository('entities\User')->find($userId);
        $pickupAddress = $this->em->getRepository('entities\Delivery')->find((int)$deliveryAddress);
        $pickup = empty($address) ? true : false;
        $order->setUser($user);
        $order->setUserName(!empty($userName) ? $userName : '');
        $order->setUserEmail(!empty($userEmail) ? $userEmail : '');
        $order->setUserPhone(!empty($userPhone) ? $userPhone : '');
        $order->setUserAddress($address);
        $order->setPickup($pickup);
        $order->setComments($comments);
        $order->setDelivery('');
        $order->setDeliveryDate($deliveryDate);
        $order->setDeliveryHours($deliveryHours);
        $order->setPickupAddress($pickupAddress);
        $order->setCreateDate(new \DateTime);
        $order->setPayment($payment);
        $order->setStatus($status);

        $amount = 0.0;
        // save order's products
        if (!empty($products) && is_array($products)) {
            foreach ($products as $product) {
                if (!empty($product) && $product instanceof ProjectProduct) {
                    $orderProduct = new OrdersProducts();
                    $orderProduct->setProductId($product->getId());
                    $orderProduct->setProduct($product);
                    $price = $product->getPrice2() > 0 ? $product->getPrice2() : $product->getPrice();
                    $orderProduct->setPrice($price);
                    $orderProduct->setQuantity($product->count);
                    $order->addProduct($orderProduct);
                    $amount += $price * $product->count;
                }
            }
        }
        
        $orderStatus = new OrdersStatuses();
        $orderStatus->setOrder($order);
        $orderStatus->setStatusId(1);
        $order->addStatus($orderStatus);
        
        $order->setAmount($amount);
        $this->em->persist($order);
        $this->em->flush();

        return $order->getId();
    }

    /**
     * Get order by ID
     *
     * @param integer $id
     * @return \entities\Order|null
     */
    public function getOrderById($id)
    {
        $result = false;

        if (!empty($id) && is_int($id)) {
            $result = $this->em->getRepository('entities\Order')->findOneById($id);
        }

        return $result;
    }

    /**
     * Get user's orders limited by date
     *
     * @param integer $userId
     * @param DateTime|string $limit
     * @return boolean|array
     */
    public function getOrdersByUserId($userId, $limit = null)
    {
        $result = false;
        $userId = clsCommon::isInt($userId);
        if ($userId > 0) {
            $sql = "SELECT o FROM entities\Order o JOIN o.user u WHERE u.id = :userId AND o.status > 0";
            $params = array('userId' => $userId);
            if (!empty($limit)) {
                $sql .= " AND o.createDate > :create";
                $params['create'] = $limit;
            }
            $sql .= " ORDER by o.createDate DESC, o.id DESC";

            $query = $this->em->createQuery($sql);
            $query->setParameters($params);

            $result = $query->getResult();
        }

        return $result;
    }

    /**
     * Update order item
     *
     * @param array $data
     * @return bool
     */
    public function updateOrder($data = array())
    {
        $return = false;
        if (!empty($data) && is_array($data)) {
            $orderId = (int)$data['id'];
            unset($data['id']);

            $order = $this->em->getRepository('entities\Order')->find($orderId);
            if ($order && !empty($data)) {
                if (isset($data['status'])) {
                    $order->setStatus((int)$data['status']);
                }
                //TODO: Add other fields
                
                $orderStatus = new OrdersStatuses();
                $orderStatus->setOrder($order);
                $orderStatus->setStatusId((int)$data['status']);
                $order->addStatus($orderStatus);
                
                $this->em->persist($order);
                $this->em->flush();

                $return = true;
            }
        }
        return $return;
    }
}
