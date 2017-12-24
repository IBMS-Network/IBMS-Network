<?php

namespace classes;

use classes\core\clsCommon;
use classes\core\clsDB;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query;
use engine\clsAdminEntity;
use engine\modules\admin\clsAdminCommon;
use entities\Admin;
use entities\AclRole;

/**
 * Prepare CRUD methods for working under ORM class \entities\Admin
 * @author Anatoly.Bogdanov
 *
 */
class clsAdmin extends clsAdminEntity
{

    /**
     * self object
     * @var clsAdminRoles
     */
    private static $instance = null;

    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
    private $em;

    /**
     * @var string $entity
     */
    private $entity;

    /**
     * Constructor of the class.
     * Set entity name, get ORM Entity Manager object
     */
    public function __construct()
    {
        parent::__construct();
        $this->entity = clsAdminCommon::getAdminMessage('admin', ADMIN_ENTITIES_BLOCK);
        $this->em = clsDB::getInstance();
    }

    /**
     * Singleton
     * @return NULL|\classes\clsAdmin
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new clsAdmin();
        }
        return self::$instance;
    }

    /**
     * Get list of Admins
     *
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
    public function getAdminsList($page = 1, $limit = DEF_PAGING_NUM, $sort = '', $sorter = 'desc', $filter = array())
    {
        $db = $this->em->createQueryBuilder();
        $db->select('user')->from('entities\Admin', 'user');
        $whereClause = $this->getElmFilter($filter, 'entities\Admin', 'user', array('role'));
        if (!empty($whereClause)) {
            $db->where($whereClause);
        }
        if (!empty($sort) && in_array($sorter, array('asc', 'desc'))) {
            $db->orderBy('user.'.$sort , $sorter);
        }
        $db->setFirstResult(((int)$page - 1) * $limit);
        $db->setMaxResults((int)$limit);

        return $db->getQuery()->getResult();
    }

    /**
     * Get count of the admins list
     * @param array $filter
     * array of filters, where key of element is name of filtering field
     * @return mixed
     */
    public function getAdminsListCount($filter = array())
    {
        $whereClause = $this->getElmFilter($filter, 'entities\Admin', 'u', array('role'));
        if (!empty($whereClause)) {
            $whereClause = " WHERE " . $whereClause;
        }
        $query = $this->em->createQuery("SELECT COUNT(u) FROM entities\Admin u".$whereClause);
        return $query->getSingleScalarResult();
    }

    /**
     * Authorization - get Admin data by login & pass
     * @param string $login
     * login of the Administrator
     * @param string $pass
     * password of the Administrator
     * @return bool|mixed
     */
    public function getAdminDataByAuth($login, $pass)
    {
        $res = false;
        if (!empty($login) && !empty($pass)) {
            $login = htmlspecialchars($login);
            $qb = $this->em->createQueryBuilder();
            $qb->select('ad')->from('entities\Admin', 'ad')->where('ad.login = :login')->setParameter(
                'login',
                $login
            )->leftJoin('entities\AclRole', 'r', Join::WITH, $qb->expr()->eq('ad.role', 'r.id'))->leftJoin(
                'entities\AclPermissionrole',
                'rp',
                Join::WITH,
                $qb->expr()->eq('ad.role', 'rp.roleId')
            )->leftJoin('entities\AclPermission', 'p', Join::WITH, $qb->expr()->eq('rp.permId', 'p.id'))->setMaxResults(
                1
            );
            $res = $qb->getQuery()->getOneOrNullResult();
            if ($res) {
                if (!password_verify($pass, $res->getPassword())) {
                    $res = false;
                }
            }
        }

        return $res;
    }

    /**
     * Get Admin data by ID
     * @param integer $id
     * administrator id
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getAdminById($id)
    {
        return $this->em->getRepository('entities\Admin')->find(clsCommon::isInt($id));
    }

    /**
     * Update administrator data
     * @param integer $id
     * identificator of the administrator
     * @param string $login
     * login of the administrator
     * @param string $password
     * password of the administrator. Optional.
     * @param Aclrole $role
     * role of admin
     * @return boolean
     * true - successfuly saved changes in the administrator profile, false - we have some errors
     */
    public function updateAdmin($id, $login, $password = '', $role)
    {
        $res = false;
        if (clsCommon::isInt($id) > 0 && !empty($login) && clsCommon::isInt($role) > 0) {
            $db = $this->em->createQueryBuilder();
            $db->select('ad')->from('entities\Admin', 'ad')
                ->where('ad.id != :identifier')->setParameter('identifier', clsCommon::isInt($id))
                ->andWhere('ad.login = :login')->setParameter('login', $login);
            $_admin = $db->getQuery()->getResult();
            if ($_admin) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $login)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                $admin = $this->em->getRepository('entities\Admin')->find(clsCommon::isInt($id));
                $admin->setLogin($login);
                if (!empty($password)) {
                    $pass = $this->hashAdminPassword($password);
                    $admin->setPassword($pass);
                }
                $role = $this->em->getRepository('entities\AclRole')->find(clsCommon::isInt($role));
                $admin->setRole($role);

                $this->em->persist($admin);
                $this->em->flush();
                $res = true;
            }
        }

        return $res;
    }

    /**
     * Hash Admin password
     * @param string $password
     * clear password
     * @return string
     */
    private function hashAdminPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, array("cost" => 10));
    }

    /**
     * Delete administrator
     * @param integer $id
     * @return boolean
     */
    public function deleteAdmin($id)
    {
        $res = false;
        if (clsCommon::isInt($id) > 0) {
            $admin = $this->em->getRepository('entities\Admin')->find(clsCommon::isInt($id));
            $this->em->remove($admin);
            $this->em->flush();
            $res = true;
        }
        return $res;
    }

    /**
     * Add administrator
     * @param string $login
     * login of the Administrator
     * @param string $password
     * passwotrd of the Administrator
     * @param integer $role
     * role ID of the Administrator
     * @return boolean
     */
    public function addAdmin($login, $password, $role)
    {
        $res = false;
        if (!empty($login) && !empty($password) && clsCommon::isInt($role) > 0) {
            $_admin = $this->em->getRepository('entities\Admin')->findBy(array('login' => $login));
            if ($_admin) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $login)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                $admin = new Admin();
                $admin->setLogin($login);
                $admin->setPassword($this->hashAdminPassword($password));
                $role = $this->em->getRepository('entities\AclRole')->find(clsCommon::isInt($role));
                if($role instanceof AclRole) {
                    $admin->setRole($role);
                }
                $this->em->persist($admin);
                $this->em->flush();
                $res = true;
            }
        }
        return $res;
    }
}