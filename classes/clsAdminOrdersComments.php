<?php
namespace classes;

use classes\core\clsCommon;
use classes\core\clsDB;
use engine\clsAdminEntity;
use engine\modules\admin\clsAdminCommon;
use entities\Order;
use entities\Admin;
use Doctrine\ORM\Query\Expr\Join as DoctrineJoin;
use entities\OrdersComments;

/**
 * Prepare CRUD methods for working under ORM class \entities\OrderComments
 * @author Anatoly.Bogdanov
 *
 */
class clsAdminOrdersComments extends clsAdminEntity
{

    /**
     * Self object
     * @var clsAdminOrdersComments $instance
     */
    private static $instance = null;

    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
    private $em;

    /**
     * Singleton
     * @return NULL|\classes\clsAdminOrdersComments
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new clsAdminOrdersComments();
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
     * Add Comment
     * @param integer $id
     * id of the Order
     * @param string $comment
     * order comment
     * @return boolean
     */
    public function addComment(Order $order, Admin $admin, $comment = '')
    {
        $entity = new OrdersComments();
        $entity->setAdmin($admin);
        $entity->setOrder($order);
        $entity->setComment($comment);
        $this->em->persist($entity);
        $this->em->flush();
            
        return true;
    }
}