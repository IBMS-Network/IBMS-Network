<?php
namespace classes;

use classes\core\clsCommon;
use classes\core\clsDB;
use engine\clsAdminEntity;
use engine\modules\admin\clsAdminCommon;
use entities\Param;

/**
 * Prepare CRUD methods for working under ORM class \entities\Param
 * @author Anatoly.Bogdanov
 *
 */
class clsAdminParams extends clsAdminEntity
{

    /**
     * self object
     * @var clsAdminParams
     */
    private static $instance = null;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * Constructorof the class.
     * Set entity name, get ORM Entity Manager object
     */
    public function __construct()
    {
        parent::__construct();
        $this->em = clsDB::getInstance();
        $this->entity = clsAdminCommon::getAdminMessage('param', ADMIN_ENTITIES_BLOCK);
    }

    /**
     * Singleton
     * @return NULL|\classes\clsAdminParams
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new clsAdminParams();
        }
        return self::$instance;
    }

    /**
     * get list of Params
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
    public function getParamsList($page = 1, $limit = DEF_PAGING_NUM, $sort = '', $sorter = 'desc', $filter = array())
    {
        $db = $this->em->createQueryBuilder();
        $db->select('ar')->from('entities\Param', 'ar');
        $whereClause = $this->getElmFilter($filter, 'entities\Param', 'ar');
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
     * Get count of the Params list
     * @param array $filter
     * array of filters, where key of element is name of filtering field
     * @return mixed
     */
    public function getParamsListCount($filter = array())
    {
        $whereClause = $this->getElmFilter($filter, 'entities\Param', 'ar');
        if (!empty($whereClause)) {
            $whereClause = " WHERE " . $whereClause;
        }
        $query = $this->em->createQuery("SELECT COUNT(ar) FROM entities\Param ar" . $whereClause);
        return $query->getSingleScalarResult();
    }

    /**
     * Get Param data by ID
     * @param int $id
     * ID of the Param
     * @return boolean | \Doctrine\ORM\EntityRepository
     */
    public function getParamById($id)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if ($id > 0) {
            $res = $this->em->getRepository('\entities\Param')->find($id);
        }
        return $res;
    }

    /**
     * Update Param
     * @param int $id
     * ID of the Param
     * @param string $name
     * name of the Param
     * @param string $value
     * value
     * @return boolean
     */
    public function updateParam($id, $name, $value)
    {
        $res = false;
        if (clsCommon::isInt($id) > 0 && !empty($name)) {
            $db = $this->em->createQueryBuilder();
            $db->select('aclr')->from('entities\Param', 'aclr')
                ->where('aclr.id != :identifier')->setParameter('identifier', clsCommon::isInt($id))
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
                /** @var Param $Param */
                $Param = $this->em->getRepository('\entities\Param')->find(clsCommon::isInt($id));
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
     * Delete Param
     * @param int $id
     * identificator of the Param
     * @return boolean
     */
    public function deleteParam($id)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if ($id > 0) {
            $Param = $this->em->getRepository('\entities\Param')->find($id);
            $this->em->remove($Param);
            $this->em->flush();
            $res = true;
        }
        return $res;
    }

    /**
     * Add Param
     * @param string $name
     * name of the Param
     * @param string $value
     * value
     * @return boolean | integer
     */
    public function addParam($name, $value)
    {
        $_Param = $this->em->getRepository('entities\Param')->findBy(array('name' => $name));
        $res = false;
        if (!$_Param) {
            $ParamEntity = new Param();
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