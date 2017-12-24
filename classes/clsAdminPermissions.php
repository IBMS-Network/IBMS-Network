<?php
namespace classes;

use classes\core\clsCommon;
use classes\core\clsDB;
use engine\clsAdminEntity;
use engine\modules\admin\clsAdminCommon;

/**
 * Prepare CRUD methods for working under ORM class \entities\AclPermission
 * @author Anatoly.Bogdanov
 *
 */
class clsAdminPermissions extends clsAdminEntity
{

    /**
     * Self object
     * @var clsAdminPermissions $instance
     */
    private static $instance = null;

    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
    private $em;

    /**
     * Singleton
     * @return NULL|\classes\clsAdminPermissions
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new clsAdminPermissions();
        }
        return self::$instance;
    }

    /**
     * Constructorof the class.
     * Set entity name, get ORM Entity Manager object
     */
    public function __construct()
    {
        parent::__construct();
        $this->entity = clsAdminCommon::getAdminMessage('permission', ADMIN_ENTITIES_BLOCK);
        $this->em = clsDB::getInstance();
    }

    /**
     * Get permissions list
     * @param int $page
     * page number
     * @param int $limit
     * limit elements per page
     * @param string $sort
     * sort field name
     * @param string $sorter
     * 'asc' or 'desc'
     * @param array $filter
     * array of filters, where key of element is name of filtering field
     * @return array
     */
    public function getPermissionsList($page = 1, $limit = DEF_PAGING_NUM, $sort = '', $sorter = 'desc', $filter = array())
    {
        $db = $this->em->createQueryBuilder();
        $db->select('ap')->from('entities\AclPermission', 'ap');
        $whereClause = $this->getElmFilter($filter, 'entities\AclPermission', 'ap');
        if (!empty($whereClause)) {
            $db->where($whereClause);
        }
        if (!empty($sort) && in_array($sorter, array('asc', 'desc'))) {
            $db->orderBy('ap.'.$sort , $sorter);
        }
        $db->setFirstResult(((int)$page - 1) * $limit);
        $db->setMaxResults((int)$limit);

        return $db->getQuery()->getResult();
    }

    /**
     * Get count of the permissions list
     * @param array $filter
     * array of filters, where key of element is name of filtering field
     * @return mixed
     */
    public function getPermissionsListCount($filter = array())
    {
        $whereClause = $this->getElmFilter($filter, 'entities\AclPermission', 'ap');
        if (!empty($whereClause)) {
            $whereClause = " WHERE " . $whereClause;
        }
        $query = $this->em->createQuery("SELECT COUNT(ap) FROM entities\AclPermission ap".$whereClause);
        return $query->getSingleScalarResult();
    }

    /**
     * Get permission by ID
     *
     * @param integer $id
     *
     * @return FALSE | \Doctrine\ORM\EntityRepository
     */
    public function getPermissionById($id)
    {
        $res = false;
        if (clsCommon::isInt($id) > 0) {
            $res = $this->em->getRepository('entities\AclPermission')->find(clsCommon::isInt($id));
        }
        return $res;
    }

    /**
     * Add Permission
     * @param string $name
     * name of the Permission
     * @return boolean
     */
    public function addPermission($name)
    {
        $res = false;
        if (!empty($name)) {

            $_perm = $this->em->getRepository('entities\AclPermission')->findBy(array('name' => $name));
            if ($_perm) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $name)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                $perm = new \entities\AclPermission();
                $perm->setName($name);
                $this->em->persist($perm);
                $this->em->flush();
                $res = true;
            }
        }
        return $res;
    }

    /**
     * Update Permission
     * @param int $id
     * identificator of the Permission
     * @param string $name
     * name of the Permission
     * @return boolean
     */
    public function updatePermission($id, $name)
    {
        $res = false;
        if (clsCommon::isInt($id) > 0 && !empty($name)) {
            $db = $this->em->createQueryBuilder();
            $db->select('aclp')->from('entities\AclPermission', 'aclp')
                ->where('aclp.id != :identifier')->setParameter('identifier', clsCommon::isInt($id))
                ->andWhere('aclp.name = :name')->setParameter('name', $name);
            $_perm = $db->getQuery()->getResult();
            if ($_perm) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $name)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                $perm = $this->em->getRepository('entities\AclPermission')->find(clsCommon::isInt($id));
                $perm->setName($name);

                $this->em->persist($perm);
                $this->em->flush();
                $res = true;
            }
        }
        return $res;
    }

    /**
     * delete Permission
     * @param int $id
     * identificator of the Permission
     * @return boolean
     */
    public function deletePermission($id)
    {
        $res = false;
        if (clsCommon::isInt($id) > 0) {
            $role = $this->em->getRepository('entities\AclPermission')->find(clsCommon::isInt($id));
            $this->em->remove($role);
            $this->em->flush();
            $res = true;
        }
        return $res;
    }
}