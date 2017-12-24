<?php

namespace classes;

use classes\core\clsCommon;
use classes\core\clsDB;
use Doctrine\ORM\Query;
use engine\clsAdminEntity;
use engine\modules\admin\clsAdminCommon;
use entities\User;

/**
 * Prepare CRUD methods for working under ORM class \entities\User
 * @author Anatoly.Bogdanov
 *
 */
class clsAdminUsers extends clsAdminEntity
{

    /**
     * self object
     * @var clsUser
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
        $this->entity = clsAdminCommon::getAdminMessage('user', ADMIN_ENTITIES_BLOCK);
        $this->em = clsDB::getInstance();
    }

    /**
     * Singleton
     * @return NULL|\classes\clsAdminUsers
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new clsAdminUsers();
        }
        return self::$instance;
    }

    /**
     * Get list of Users
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
    public function getUsersList($page = 1, $limit = DEF_PAGING_NUM, $sort = '', $sorter = 'desc', $filter = array())
    {
        $db = $this->em->createQueryBuilder();
        $db->select('user')->from('entities\User', 'user');
        $whereClause = $this->getElmFilter($filter, 'entities\User', 'user');
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
     * Get count of the users list
     * @param array $filter
     * array of filters, where key of element is name of filtering field
     * @return mixed
     */
    public function getUsersListCount($filter = array())
    {
        $whereClause = $this->getElmFilter($filter, 'entities\User', 'u');
        if (!empty($whereClause)) {
            $whereClause = " WHERE " . $whereClause;
        }
        $query = $this->em->createQuery("SELECT COUNT(u) FROM entities\User u".$whereClause);
        return $query->getSingleScalarResult();
    }

    /**
     * Get User data by ID
     * @param integer $id
     * administrator id
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getUserById($id)
    {
        return $this->em->getRepository('entities\User')->find(clsCommon::isInt($id));
    }

    /**
     * Update user data
     * @param int $id
     * ID user name
     * @param string $email
     * user email
     * @param string $password
     * user password
     * @param string $firstName
     * user first name
     * @param string $lastName
     * user last name
     * @param int $sex
     * user sex
     * @param string $phone
     * user phone
     * @param string $city
     * user city
     * @param int $status
     * user status
     * @return bool
     */
    public function updateUser($id, $email, $password = '', $firstName = '', $lastName = '', $sex = 0, $phone = '', $city = '', $status = 1)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if ($id > 0 && !empty($email)) {
            $db = $this->em->createQueryBuilder();
            $db->select('ad')->from('entities\User', 'ad')
                ->where('ad.id != :identifier')->setParameter('identifier', $id)
                ->andWhere('ad.email = :email')->setParameter('email', $email);
            $_user = $db->getQuery()->getResult();
            if ($_user) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $email)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                $user = $this->em->getRepository('entities\User')->find($id);
                $user->setEmail($email)
                    ->setFirstName($firstName)
                    ->setLastName($lastName)
                    ->setSex($sex)
                    ->setPhone($phone)
                    ->setCity($city)
                    ->setStatus($status);

                if (!empty($password)) {
                    $pass = $this->hashAdminPassword($password);
                    $user->setPassword($pass);
                }

                $this->em->persist($user);
                $this->em->flush();
                $res = true;
            }
        }

        return $res;
    }

    /**
     * Hash User password
     * @param string $password
     * clear password
     * @return string
     */
    private function hashAdminPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, array("cost" => 10));
    }

    /**
     * Delete user
     * @param integer $id
     * @return boolean
     */
    public function deleteUser($id)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if ( $id > 0) {
            $user = $this->em->getRepository('entities\User')->find($id);
            $this->em->remove($user);
            $this->em->flush();
            $res = true;
        }
        return $res;
    }

    /**
     * Add user
     * @param string $email
     * user email
     * @param string $password
     * user password
     * @param string $firstName
     * user first name
     * @param string $lastName
     * user last name
     * @param int $sex
     * user sex
     * @param string $phone
     * user phone
     * @param string $city
     * user city
     * @param int $status
     * user status
     * @return bool
     */
    public function addUser($email, $password = '', $firstName = '', $lastName = '', $sex = 0, $phone = '', $city = '', $status = 1)
    {
        $res = false;
        if (!empty($email) && !empty($password)) {
            $_user = $this->em->getRepository('entities\User')->findBy(array('email' => $email));
            if ($_user) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $email)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                $user = new User();
                $user->setEmail($email)
                    ->setFirstName($firstName)
                    ->setLastName($lastName)
                    ->setSex($sex)
                    ->setPhone($phone)
                    ->setCity($city)
                    ->setStatus($status)
                    ->setPassword($this->hashAdminPassword($password));
                $this->em->persist($user);
                $this->em->flush();
                $res = true;
            }
        }
        return $res;
    }
}