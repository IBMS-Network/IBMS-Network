<?php
namespace classes;

use classes\core\clsCommon;
use classes\core\clsDB;
use engine\clsAdminEntity;
use engine\modules\admin\clsAdminCommon;
use entities\Emailtemp;

/**
 * Prepare CRUD methods for working under ORM class \entities\Emailtemp
 * @author Anatoly.Bogdanov
 *
 */
class clsAdminEmailTemps extends clsAdminEntity
{

    /**
     * self object
     * @var clsAdminEmailTemps
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
        $this->entity = clsAdminCommon::getAdminMessage('emailtemp', ADMIN_ENTITIES_BLOCK);
    }

    /**
     * Singleton
     * @return NULL|\classes\clsAdminEmailTemps
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new clsAdminEmailTemps();
        }
        return self::$instance;
    }

    /**
     * get list of EmailTemps
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
    public function getEmailTempsList($page = 1, $limit = DEF_PAGING_NUM, $sort = '', $sorter = 'desc', $filter = array())
    {
        $db = $this->em->createQueryBuilder();
        $db->select('ar')->from('entities\Emailtemp', 'ar');
        $whereClause = $this->getElmFilter($filter, 'entities\Emailtemp', 'ar');
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
     * Get count of the EmailTemps list
     * @param array $filter
     * array of filters, where key of element is name of filtering field
     * @return mixed
     */
    public function getEmailTempsListCount($filter = array())
    {
        $whereClause = $this->getElmFilter($filter, 'entities\Emailtemp', 'ar');
        if (!empty($whereClause)) {
            $whereClause = " WHERE " . $whereClause;
        }
        $query = $this->em->createQuery("SELECT COUNT(ar) FROM entities\Emailtemp ar" . $whereClause);
        return $query->getSingleScalarResult();
    }

    /**
     * Get Emailtemp data by ID
     * @param int $id
     * ID of the Emailtemp
     * @return boolean | \Doctrine\ORM\EntityRepository
     */
    public function getEmailtempById($id)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if ($id > 0) {
            $res = $this->em->getRepository('\entities\Emailtemp')->find($id);
        }
        return $res;
    }

    /**
     * Update Emailtemp
     * @param int $id
     * ID of the Emailtemp
     * @param string $name
     * name of the Emailtemp
     * @param string $value
     * value
     * @param string $email
     * email
     * @param string $subject
     * subject
     * @return boolean
     */
    public function updateEmailtemp($id, $name, $value, $email, $subject)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if ($id > 0 && !empty($name) && !empty($email) && !empty($subject)) {
            $db = $this->em->createQueryBuilder();
            $db->select('aclr')->from('entities\Emailtemp', 'aclr')
                ->where('aclr.id != :identifier')->setParameter('identifier', $id)
                ->andWhere('aclr.name = :name')->setParameter('name', $name);
            $_Emailtemp = $db->getQuery()->getResult();
            if ($_Emailtemp) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $name)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                /** @var Emailtemp $Emailtemp */
                $Emailtemp = $this->em->getRepository('\entities\Emailtemp')->find($id);
                $Emailtemp->setName($name)
                      ->setValue($value)
                      ->setEmail($email)
                      ->setSubject($subject);
                $this->em->persist($Emailtemp);
                $this->em->flush();
                $res = true;
            }
        }
        return $res;
    }

    /**
     * Delete Emailtemp
     * @param int $id
     * identificator of the Emailtemp
     * @return boolean
     */
    public function deleteEmailtemp($id)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if ($id > 0) {
            $Emailtemp = $this->em->getRepository('\entities\Emailtemp')->find($id);
            $this->em->remove($Emailtemp);
            $this->em->flush();
            $res = true;
        }
        return $res;
    }

    /**
     * Add Emailtemp
     * @param string $name
     * name of the Emailtemp
     * @param string $value
     * value
     * @param string $email
     * email
     * @param string $subject
     * subject
     * @return boolean | integer
     */
    public function addEmailtemp($name, $value, $email, $subject)
    {
        $_Emailtemp = $this->em->getRepository('entities\Emailtemp')->findBy(array('name' => $name));
        $res = false;
        if (!$_Emailtemp && !empty($name) && !empty($email) && !empty($subject)) {
            $EmailtempEntity = new Emailtemp();
            $EmailtempEntity->setName($name)
                        ->setValue($value)
                        ->setEmail($email)
                        ->setSubject($subject);
            $this->em->persist($EmailtempEntity);
            $this->em->flush();
            $res = $EmailtempEntity->getId();
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