<?php
namespace classes;

use classes\core\clsCommon;
use classes\core\clsDB;
use engine\clsAdminEntity;
use engine\modules\admin\clsAdminCommon;

/**
 * Prepare CRUD methods for working under ORM class \entities\AclRole
 * @author Anatoly.Bogdanov
 *
 */
class clsAdminRoles extends clsAdminEntity
{

    /**
     * self object
     * @var clsAdminRoles
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
        $this->entity = clsAdminCommon::getAdminMessage('role', ADMIN_ENTITIES_BLOCK);
    }

    /**
     * Singleton
     * @return NULL|\classes\clsAdminRoles
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new clsAdminRoles();
        }
        return self::$instance;
    }

    /**
     * get list of roles
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
    public function getRolesList($page = 1, $limit = DEF_PAGING_NUM, $sort = '', $sorter = 'desc', $filter = array())
    {
        $db = $this->em->createQueryBuilder();
        $db->select('ar')->from('entities\AclRole', 'ar');
        $whereClause = $this->getElmFilter($filter, 'entities\AclRole', 'ar');
        if (!empty($whereClause)) {
            $db->where($whereClause);
        }
        if (!empty($sort) && in_array($sorter, array('asc', 'desc'))) {
            $db->orderBy('ar.'.$sort , $sorter);
        }
        $db->setFirstResult(((int)$page - 1) * $limit);
        $db->setMaxResults((int)$limit);

        return $db->getQuery()->getResult();
    }

    /**
     * Get count of the roles list
     * @param array $filter
     * array of filters, where key of element is name of filtering field
     * @return mixed
     */
    public function getRolesListCount($filter = array())
    {
        $whereClause = $this->getElmFilter($filter, 'entities\AclRole', 'ar');
        if (!empty($whereClause)) {
            $whereClause = " WHERE " . $whereClause;
        }
        $query = $this->em->createQuery("SELECT COUNT(ar) FROM entities\AclRole ar".$whereClause);
        return $query->getSingleScalarResult();
    }

    /**
     * Get Role data by ID
     * @param int $id
     * identificator of the Role
     * @return boolean | \Doctrine\ORM\EntityRepository
     */
    public function getRoleById($id)
    {
        $res = false;
        if (clsCommon::isInt($id) > 0) {
            $res = $this->em->getRepository('\entities\AclRole')->find(clsCommon::isInt($id));
        }
        return $res;
    }

    /**
     * Update role
     * @param int $id
     * identificator of the Role
     * @param string $name
     * name of the Role
     * @return boolean
     */
    public function updateRole($id, $name, $perm)
    {
        $res = false;
        if (clsCommon::isInt($id) > 0 && !empty($name)) {
            $db = $this->em->createQueryBuilder();
            $db->select('aclr')->from('entities\AclRole', 'aclr')
                ->where('aclr.id != :identifier')->setParameter('identifier', clsCommon::isInt($id))
                ->andWhere('aclr.name = :name')->setParameter('name', $name);
            $_role = $db->getQuery()->getResult();
            if ($_role) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $name)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                $role = $this->em->getRepository('\entities\AclRole')->find(clsCommon::isInt($id));
                $role->setName($name);
                $permsIds = array_keys($perm);
                array_walk(
                    $permsIds,
                    function (&$val) {
                        $val = clsCommon::isInt($val);
                    }
                );
                $perms = '';
                $perms = $this->em->getRepository('\entities\AclPermission')->findBy(array('id' => $permsIds));
                $role->setPermissions($perms);
                $this->em->persist($role);
                $this->em->flush();
                $res = true;
            }
        }
        return $res;
    }

    /**
     * Delete Role
     * @param int $id
     * identificator of the Role
     * @return boolean
     */
    public function deleteRole($id)
    {
        $res = false;
        if (clsCommon::isInt($id) > 0) {
            $role = $this->em->getRepository('\entities\AclRole')->find(clsCommon::isInt($id));
            $this->em->remove($role);
            $this->em->flush();
            $res = true;
        }
        return $res;
    }

    /**
     * Add Role
     * @param string $name
     * name of the Role
     * @param array $perms
     * @return boolean | integer
     */
    public function addRole($name, $perms)
    {
        $_role = $this->em->getRepository('entities\AclRole')->findBy(array('name' => $name));
        if ($_role) {
            $error = clsAdminCommon::getAdminMessage(
                'error_entity_name_exists',
                ADMIN_ERROR_BLOCK,
                array('{%entity}' => $this->entity, '{%entityname}' => $name)
            );
            $this->errors->setError($error, 1, false, true);
        } else {
            $permsIds = array_keys($perms);
            array_walk(
                $permsIds,
                function (&$val) {
                    $val = clsCommon::isInt($val);
                }
            );
            $perms = $this->em->getRepository('\entities\AclPermission')->findBy(array('id' => $permsIds));
            $roleEntity = new \entities\AclRole();
            $roleEntity->setName($name);
            $roleEntity->setPermissions($perms);
            $this->em->persist($roleEntity);
            $this->em->flush();
            $res = $roleEntity->getId();
        }
        return $res;
    }
}