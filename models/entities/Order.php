<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use DateTime;
use Gedmo\Mapping\Annotation as Gedmo;
use entities\OrderPaymentTypes;

/**
 * Service
 *
 * @ORM\Table(name="orders")
 * @ORM\Entity
 */
class Order extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    protected $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="user_address", type="string", nullable=true)
     */
    protected $userAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery", type="string", nullable=true)
     */
    protected $delivery;

    /**
     * @var string
     *
     * @ORM\Column(name="user_name", type="string", nullable=false)
     */
    protected $userName;

    /**
     * @var string
     *
     * @ORM\Column(name="user_email", type="string", nullable=false)
     */
    protected $userEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="user_phone", type="string", nullable=false)
     */
    protected $userPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="comments", type="string", nullable=true)
     */
    protected $comments;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="delivery_date", type="datetime", nullable=true)
     */
    protected $deliveryDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime", nullable=false)
     */
    protected $createDate;

    /**
     * @var string
     *
     * @ORM\Column(name="payment", type="string", nullable=false)
     */
    protected $payment;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    protected $status = 0;

    /**
     * @ORM\OneToMany(targetEntity="OrdersProducts", mappedBy="order", cascade={"persist"})
     **/
    protected $products;

    /**
     * @var double
     *
     * @ORM\Column(name="amount", type="float", nullable=false)
     */
    protected $amount = 0.0;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    protected $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    protected $updated;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="pickup", type="boolean", nullable=false)
     */
    protected $pickup = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="delivery_hours", type="integer", nullable=true)
     */
    protected $deliveryHours;
    
    /**
     * @var Delivery
     *
     * @ORM\ManyToOne(targetEntity="Delivery")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pickup_id", referencedColumnName="id")
     * })
     */
    protected $pickupAddress;
    
    /**
     * @ORM\OneToMany(targetEntity="OrdersStatuses", mappedBy="order", cascade={"persist"})
     **/
    protected $statuses;
    
    /**
     * @ORM\OneToMany(targetEntity="OrdersComments", mappedBy="order")
     **/
    protected $orderComments;
    
    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->statuses = new ArrayCollection();
        $this->orderComments = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getUserAddress()
    {
        return $this->userAddress;
    }

    /**
     * @return string
     */
    public function getDelivery()
    {
        return $this->delivery;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }

    /**
     * @return string
     */
    public function getUserPhone()
    {
        return $this->userPhone;
    }

    /**
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param $id
     * @return Order
     */
    public function setId($id)
    {
        $this->id = (int)$id;
        return $this;
    }

    /**
     * @param $userId
     * @return Order
     */
    public function setUserId($userId)
    {
        $this->userId = (int)$userId;
        return $this;
    }

    /**
     * @param $userAddress
     * @return Order
     */
    public function setUserAddress($userAddress)
    {
        $this->userAddress = $userAddress;
        return $this;
    }

    /**
     * @param $delivery
     * @return Order
     */
    public function setDelivery($delivery)
    {
        $this->delivery = $delivery;
        return $this;
    }

    /**
     * @param $userName
     * @return Order
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
        return $this;
    }

    /**
     * @param $userEmail
     * @return Order
     */
    public function setUserEmail($userEmail)
    {
        $this->userEmail = $userEmail;
        return $this;
    }

    /**
     * @param $userPhone
     * @return Order
     */
    public function setUserPhone($userPhone)
    {
        $this->userPhone = $userPhone;
        return $this;
    }

    /**
     * @param $comments
     * @return Order
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }

    /**
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * @return string
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param $deliveryDate
     * @return Order
     */
    public function setDeliveryDate(\DateTime $deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;
        return $this;
    }

    /**
     * @param $createDate
     * @return Order
     */
    public function setCreateDate(\DateTime $createDate)
    {
        $this->createDate = $createDate;
        return $this;
    }

    /**
     * @param $payment
     * @return Order
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;
        return $this;
    }

    /**
     * @param $status
     * @return Order
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param OrdersProducts $product
     * @return Order
     */
    public function addProduct(OrdersProducts $product)
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setOrder($this);
        }
        return $this;
    }
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getStatuses()
    {
        return $this->statuses;
    }

    /**
     * @param OrdersStatuses $status
     * @return Order
     */
    public function addStatus(OrdersStatuses $status)
    {
        if (!$this->statuses->contains($status)) {
            $this->statuses->add($status);
            $status->setOrder($this);
        }
        return $this;
    }
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOrderComments()
    {
        return $this->orderComments;
    }

    /**
     * @param OrdersComments $comment
     * @return Order
     */
    public function addComment(OrdersComments $comment)
    {
        if (!$this->orderComments->contains($comment)) {
            $this->orderComments->add($comment);
            $comment->setOrder($this);
        }
        return $this;
    }

    /**
     * @param float $amount
     * @return Order
     */
    public function setAmount($amount)
    {
        $this->amount = (double)$amount;
        return $this;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }
    
    /**
     * Set delivery hours
     * 
     * @param int $deliveryHours
     * @return Order
     */
    public function setDeliveryHours($deliveryHours)
    {
        $this->deliveryHours = (int)$deliveryHours;
        return $this;
    }

    /**
     * Get delivery hours
     * 
     * @return int
     */
    public function getDeliveryHours()
    {
        return $this->deliveryHours;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Order
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     *
     * @return Order
     */
    public function setUpdated($modified)
    {
        if (is_string($modified)) {
            $modified = new \DateTime($modified);
        } elseif (is_array($modified)) {
            $modified = \DateTime::__set_state($modified);
        }

        $this->updated = $modified;

        return $this;
    }
    
    /**
     * Get pickup
     *
     * @return boolean
     */
    function getPickup()
    {
        return $this->pickup;
    }

    /**
     * Set pickup
     *
     * @param boolean $pickup
     *
     * @return Order
     */
    function setPickup($pickup)
    {
        $this->pickup = $pickup;
        return $this;
    }
    
    /**
     * Get pickup address
     *
     * @return Delivery
     */
    function getPickupAddress()
    {
        return $this->pickupAddress;
    }

    /**
     * Set pickup address
     *
     * @param Delivery $pickupAddress
     *
     * @return Order
     */
    function setPickupAddress(Delivery $pickupAddress)
    {
        $this->pickupAddress = $pickupAddress;
        return $this;
    }

    /**
     * @return array
     */
    public function getArrayCopy(){
        $result = array(
            'id' => $this->getId(),
            'create_date'=> $this->getCreateDate(),
            'payment' => $this->getPayment(),
            'delivery_date' => $this->getDeliveryDate() ? $this->getDeliveryDate() : '',
            'delivery_time' => $this->getDeliveryHours() ? $this->getDeliveryHours() : '',
            'delivery_cost' => $this->getPickup() ? 0 : DELIVERY_COST,
            'delivery_address' => $this->getUserAddress() ? $this->getUserAddress() : '',
            'pickup_address' => $this->getPickupAddress() ? $this->getPickupAddress()->getValue() : '',
            'amount' => $this->getAmount(),
            'comment' => $this->getComments(),
            'status' => $this->getStatus(),
            'user_phone' => $this->getUserPhone(),
            'user_name' => $this->getUserName(),
            'user_email' => $this->getUserEmail(),
        );
        if(!empty($this->products)){
            foreach($this->products as $product){
                if($product->getProduct() instanceof Product){
                    $result['products'][] = array(
                        'count' => $product->getQuantity(),
                        'price' => $product->getPrice(),
                        'product' => $product->getProduct()->getArrayCopy()
                    );
                }
            }
        }
        if($this->user instanceof User){
            $result['user_phone'] = $this->user->getPhone();
            $result['user_name'] = $this->user->getFirstName() . ' ' . $this->user->getLastName();
            $result['user_email'] = $this->user->getEmail();
            $result['user_city'] = $this->user->getCity();
        }
        
        return $result;
    }
}
