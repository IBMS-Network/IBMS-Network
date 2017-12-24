<?php
namespace classes;

use classes\core\clsCommon;
use classes\core\clsDB;
use engine\clsAdminEntity;
use engine\modules\admin\clsAdminCommon;
use entities\Order;
use Doctrine\ORM\Query\Expr\Join as DoctrineJoin;
use entities\OrdersStatuses;

/**
 * Prepare CRUD methods for working under ORM class \entities\Order
 * @author Anatoly.Bogdanov
 *
 */
class clsAdminOrders extends clsAdminEntity
{

    /**
     * Self object
     * @var clsAdminOrders $instance
     */
    private static $instance = null;

    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
    private $em;

    /**
     * Singleton
     * @return NULL|\classes\clsAdminOrders
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new clsAdminOrders();
        }
        return self::$instance;
    }

    /**
     * Constructor of the class.
     * Set entity name, get ORM Entity Manager object
     */
    public function __construct()
    {
        parent::__construct();
        $this->entity = clsAdminCommon::getAdminMessage('order', ADMIN_ENTITIES_BLOCK);
        $this->em = clsDB::getInstance();
    }

    /**
     * Get orders list
     * @param int $page
     * page number
     * @param int $limit
     * limit elements per page
     * @param string $sort
     * sort field name
     * @param string $sorter
     * 'asc' or 'desc'
     * @param array $filter
     * @return array
     */
    public function getOrdersList($page = 1, $limit = DEF_PAGING_NUM, $sort = '', $sorter = 'desc', $filter = array())
    {
        $db = $this->em->createQueryBuilder();
        $db->select('ord')->from('entities\Order', 'ord');
        $whereClause = $this->getElmFilter($filter, 'entities\Order', 'ord', array('user'));
        if (!empty($whereClause)) {
            $db->where($whereClause);
        }
        if (!empty($sort) && in_array($sorter, array('asc', 'desc'))) {
            $db->orderBy('ord.' . $sort, $sorter);
        }
        $db->setFirstResult(((int)$page - 1) * $limit);
        $db->setMaxResults((int)$limit);

        return $db->getQuery()->getResult();
    }

    /**
     * Get count of the orders list
     * @param array $filter
     * @return mixed
     */
    public function getOrdersListCount($filter)
    {
        $whereClause = $this->getElmFilter($filter, 'entities\Order', 'ord', array('user'));
        if (!empty($whereClause)) {
            $whereClause = " WHERE " . $whereClause;
        }
        $query = $this->em->createQuery("SELECT COUNT(ord) FROM entities\Order ord" . $whereClause);
        return $query->getSingleScalarResult();
    }

    /**
     * Get order by ID
     *
     * @param integer $id
     *
     * @return FALSE | \Doctrine\ORM\EntityRepository
     */
    public function getOrderById($id)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if ($id > 0) {
            $qb = $this->em->createQueryBuilder();
            $query = $qb
                ->select('o')
                ->from('entities\Order', 'o')
                ->innerJoin(
                    'o.products',
                    'opr'
                )
                ->innerJoin(
                    'opr.product',
                    'pr'
                )
                ->where('o.id = :id')
                ->setParameter('id', $id)
                ->getQuery();

            $res = $query->getOneOrNullResult();
//            $res = $this->em->getRepository('entities\Order')->find(clsCommon::isInt($id));
        }
        return $res;
    }

    /**
     * Add Order
     * @param string $name
     * name of the Order
     * @param string $img
     * path to file
     * @param string $desc
     * description
     * @return boolean
     */
    public function addOrder($name, $img = '', $desc = '')
    {
        $res = false;
        if (!empty($name)) {

            $_order = $this->em->getRepository('entities\Order')->findBy(array('name' => $name));
            if ($_order) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $name)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                $order = new \entities\Order();
                $order->setName($name)
                    ->setDescription($desc);
                if (!empty($img)) {
                    $order->setImg($img);
                }
                $this->em->persist($order);
                $this->em->flush();
                $res = true;
            }
        }
        return $res;
    }

    /**
     * Update Order
     * @param int $id
     * identificator of the Order
     * @param string $name
     * name of the Order
     * @param string $img
     * path to file
     * @param string $desc
     * description
     * @return boolean
     */
    public function updateOrder($id, $name, $img = '', $desc = '')
    {
        $res = false;
        if (clsCommon::isInt($id) > 0 && !empty($name)) {
            $db = $this->em->createQueryBuilder();
            $db->select('c')->from('entities\Order', 'c')
                ->where('c.id != :identifier')->setParameter('identifier', clsCommon::isInt($id))
                ->andWhere('c.name = :name')->setParameter('name', $name);
            $_order = $db->getQuery()->getResult();
            if ($_order) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $name)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                $order = $this->em->getRepository('entities\Order')->find(clsCommon::isInt($id));
                $order->setName($name)
                    ->setDescription($desc);
                
                $orderStatus = new OrdersStatuses();
                $orderStatus->setOrder($order);
                $orderStatus->setStatusId($order->getStatus());
                $order->addStatus($orderStatus);
                
                $this->em->persist($order);
                $this->em->flush();
                $res = true;
            }
        }
        return $res;
    }

    /**
     * delete Order
     * @param int $id
     * identificator of the Order
     * @return boolean
     */
    public function deleteOrder($id)
    {
        $res = false;
        if (clsCommon::isInt($id) > 0) {
            $order = $this->em->getRepository('entities\Order')->find(clsCommon::isInt($id));
            $this->em->remove($order);
            $this->em->flush();
            $res = true;
        }
        return $res;
    }

    /**
     * Update Order status
     * @param int $order_id
     * ID of Order
     * @param int $status
     *
     * @return bool|\DateTime
     */
    public function setOrderStatus($order_id = 0, $status = 0)
    {
        $result = false;
        $order_id = clsCommon::isInt($order_id);
        $status = clsCommon::isInt($status);
        if ($order_id > 0 && $status > 0) {
            $order = $this->getOrderById($order_id);
            if (!empty($order) && $order instanceof Order) {
                $order->setStatus($status);
                
                $orderStatus = new OrdersStatuses();
                $orderStatus->setOrder($order);
                $orderStatus->setStatusId($status);
                $order->addStatus($orderStatus);
                
                $this->em->persist($order);
                $this->em->flush();
                $result = $order->getUpdated();
            }
        }
        return $result;

    }
}