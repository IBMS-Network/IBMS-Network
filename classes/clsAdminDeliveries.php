<?php
namespace classes;

use classes\core\clsCommon;
use classes\core\clsDB;
use engine\clsAdminEntity;
use engine\modules\admin\clsAdminCommon;
use entities\Delivery;

/**
 * Prepare CRUD methods for working under ORM class \entities\Param
 * @author Anatoly.Bogdanov
 *
 */
class clsAdminDeliveries extends clsAdminEntity
{

    /**
     * self object
     * @var clsAdminDeliveries
     */
    private static $instance = null;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * Constructor of the class.
     * Set entity name, get ORM Entity Manager object
     */
    public function __construct()
    {
        parent::__construct();
        $this->em = clsDB::getInstance();
        $this->entity = clsAdminCommon::getAdminMessage('delivery', ADMIN_ENTITIES_BLOCK);
    }

    /**
     * Singleton
     * @return NULL|\classes\clsAdminDeliveries
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new clsAdminDeliveries();
        }
        return self::$instance;
    }

    /**
     * get list of Deliveries
     *
     * @param int $page
     * page number
     * @param int $limit
     * limit of an elements per page
     * @param string $sort
     * sort field name
     * @param string $sorter
     * 'asc' or 'desc'
     * @param array $filter
     * array of filters, where key of element is name of filtering field
     * @return array
     */
    public function getDeliveriesList($page = 1, $limit = DEF_PAGING_NUM, $sort = '', $sorter = 'desc', $filter = array())
    {
        $db = $this->em->createQueryBuilder();
        $db->select('ar')->from('entities\Delivery', 'ar');
        $whereClause = $this->getElmFilter($filter, 'entities\Delivery', 'ar');
        if (!empty($whereClause)) {
            $db->where($whereClause);
        }
        if (!empty($sort) && in_array($sorter, array('asc', 'desc'))) {
            $db->orderBy('ar.' . $sort, $sorter);
        }
        $db->setFirstResult(((int)$page - 1) * $limit);
        $db->setMaxResults((int)$limit);

        return $db->getQuery()->getResult();
    }

    /**
     * Get count of the Deliveries list
     * @param array $filter
     * array of filters, where key of element is name of filtering field
     * @return mixed
     */
    public function getDeliveriesListCount($filter = array())
    {
        $whereClause = $this->getElmFilter($filter, 'entities\Delivery', 'ar');
        if (!empty($whereClause)) {
            $whereClause = " WHERE " . $whereClause;
        }
        $query = $this->em->createQuery("SELECT COUNT(ar) FROM entities\Delivery ar" . $whereClause);
        return $query->getSingleScalarResult();
    }

    /**
     * Get Delivery data by ID
     * @param int $id
     * ID of the Param
     * @return boolean | \Doctrine\ORM\EntityRepository
     */
    public function getDeliveryById($id)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if ($id > 0) {
            $res = $this->em->getRepository('\entities\Delivery')->find($id);
        }
        return $res;
    }

    /**
     * Update Delivery
     * @param int $id
     * ID of the Delivery
     * @param string $name
     * name of the Delivery
     * @param string $value
     * value
     * @return boolean
     */
    public function updateDelivery($id, $name, $value)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if ($id > 0 && !empty($name)) {
            $db = $this->em->createQueryBuilder();
            $db->select('aclr')->from('entities\Delivery', 'aclr')
                ->where('aclr.id != :identifier')->setParameter('identifier', $id)
                ->andWhere('aclr.name = :name')->setParameter('name', $name);
            $_Param = $db->getQuery()->getResult();
            if ($_Param) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $name)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                /** @var Delivery $Param */
                $Param = $this->em->getRepository('\entities\Delivery')->find($id);
                $Param->setName($name)
                      ->setValue($value);
                $this->em->persist($Param);
                $this->em->flush();
                $res = true;
            }
        }
        return $res;
    }

    /**
     * Delete Delivery
     * @param int $id
     * identificator of the Delivery
     * @return boolean
     */
    public function deleteDelivery($id)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if ($id > 0) {
            $Param = $this->em->getRepository('\entities\Delivery')->find($id);
            $this->em->remove($Param);
            $this->em->flush();
            $res = true;
        }
        return $res;
    }

    /**
     * Add Delivery
     * @param string $name
     * name of the Delivery
     * @param string $value
     * value
     * @return boolean | integer
     */
    public function addDelivery($name, $value)
    {
        $_Param = $this->em->getRepository('entities\Delivery')->findBy(array('name' => $name));
        $res = false;
        if (!$_Param) {
            $ParamEntity = new Delivery();
            $ParamEntity->setName($name)
                        ->setValue($value);
            $this->em->persist($ParamEntity);
            $this->em->flush();
            $res = $ParamEntity->getId();
        } else {
            $error = clsAdminCommon::getAdminMessage(
                'error_entity_name_exists',
                ADMIN_ERROR_BLOCK,
                array('{%entity}' => $this->entity, '{%entityname}' => $name)
            );
            $this->errors->setError($error, 1, false, true);
        }
        return $res;
    }
}