<?php
namespace classes;

use classes\core\clsCommon;
use classes\core\clsDB;
use engine\clsAdminEntity;
use engine\modules\admin\clsAdminCommon;
use entities\Brand;
use entities\Country;

/**
 * Prepare CRUD methods for working under ORM class \entities\Brand
 * @author Anatoly.Bogdanov
 *
 */
class clsAdminBrands extends clsAdminEntity
{

    /**
     * Self object
     * @var clsAdminBrands $instance
     */
    private static $instance = null;

    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
    private $em;

    /**
     * Singleton
     * @return NULL|\classes\clsAdminBrands
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new clsAdminBrands();
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
        $this->entity = clsAdminCommon::getAdminMessage('brand', ADMIN_ENTITIES_BLOCK);
        $this->em = clsDB::getInstance();
    }

    /**
     * Get brands list
     * @param int $page
     * @param int $limit
     * @param string $sort
     * @param string $sorter
     * @param array $filter
     * @return array
     */
    public function getBrandsList($page = 1, $limit = DEF_PAGING_NUM, $sort = '', $sorter = 'desc', $filter = array())
    {
        $db = $this->em->createQueryBuilder();
        $db->select('c')->from('entities\Brand', 'c');
        $whereClause = $this->getElmFilter($filter, 'entities\Brand', 'c', array('country'));
        if (!empty($whereClause)) {
            $db->where($whereClause);
        }
        if (!empty($sort) && in_array($sorter, array('asc', 'desc'))) {
            $db->orderBy('c.'.$sort , $sorter);
        }
        $db->setFirstResult(((int)$page - 1) * $limit);
        $db->setMaxResults((int)$limit);

        return $db->getQuery()->getResult();
    }

    /**
     * Get count of the brands list
     * @param array $filter
     * @return mixed
     */
    public function getBrandsListCount($filter = array())
    {
        $whereClause = $this->getElmFilter($filter, 'entities\Brand', 'c', array('country'));
        if (!empty($whereClause)) {
            $whereClause = " WHERE " . $whereClause;
        }
        $query = $this->em->createQuery("SELECT COUNT(c) FROM entities\Brand c ".$whereClause);
        return $query->getSingleScalarResult();
    }

    /**
     * Get brand by ID
     *
     * @param integer $id
     *
     * @return FALSE | \Doctrine\ORM\EntityRepository
     */
    public function getBrandById($id)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if ($id > 0) {
            $res = $this->em->getRepository('entities\Brand')->find($id);
        }
        return $res;
    }

    /**
     * Get brand by Name
     *
     * @param string $name
     *
     * @return FALSE | \Doctrine\ORM\EntityRepository
     */
    public function getBrandByName($name)
    {
        $res = false;
        if (!empty($name)) {
            $res = $this->em->getRepository('entities\Brand')->findOneBy(array('name' => $name));
        }
        return $res;
    }

    /**
     * Add Brand
     * @param string $name
     * name of the Brand
     * @param string $img
     * path to file
     * @param string $desc
     * description
     * @param int $country_id
     * country
     * @return boolean
     */
    public function addBrand($name, $img = '', $desc = '', $country_id = 0)
    {
        $res = false;
        if (!empty($name)) {

            $_brand = $this->em->getRepository('entities\Brand')->findBy(array('name' => $name));
            if ($_brand) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $name)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                $brand = new Brand();
                $brand->setName($name)
                    ->setDescription($desc);
                if (!empty($img)) {
                    $brand->setImg($img);
                }

                $country_id = clsCommon::isInt($country_id);
                if($country_id > 0){
                    $country = $this->em->getRepository('entities\Country')->find($country_id);
                    if(!empty($country) && $country instanceof Country){
                        $brand->setCountry($country);
                    }
                }
                $this->em->persist($brand);
                $this->em->flush();
                $res = $brand->getId();
            }
        }
        return $res;
    }

    /**
     * Update Brand
     * @param int $id
     * ID of the Brand
     * @param string $name
     * name of the Brand
     * @param string $img
     * path to file
     * @param string $desc
     * description
     * @param int $country_id
     * country
     * @return boolean
     */
    public function updateBrand($id, $name, $img = '', $desc = '', $country_id = 0)
    {
        $res = false;
        if (clsCommon::isInt($id) > 0 && !empty($name)) {
            $db = $this->em->createQueryBuilder();
            $db->select('c')->from('entities\Brand', 'c')
                ->where('c.id != :identifier')->setParameter('identifier', clsCommon::isInt($id))
                ->andWhere('c.name = :name')->setParameter('name', $name);
            $_brand = $db->getQuery()->getResult();
            if ($_brand) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $name)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                $brand = $this->em->getRepository('entities\Brand')->find(clsCommon::isInt($id));
                $brand->setName($name)
                    ->setDescription($desc);;
                if (!empty($img)) {
                    $brand->setImg($img);
                }
                $country_id = clsCommon::isInt($country_id);
                if($country_id > 0){
                    $country = $this->em->getRepository('entities\Country')->find($country_id);
                    if(!empty($country) && $country instanceof Country){
                        $brand->setCountry($country);
                    }
                }
                $this->em->persist($brand);
                $this->em->flush();
                $res = true;
            }
        }
        return $res;
    }

    /**
     * delete Brand
     * @param int $id
     * ID of the Brand
     * @return boolean
     */
    public function deleteBrand($id)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if ( $id > 0) {
            $role = $this->em->getRepository('entities\Brand')->find($id);
            $this->em->remove($role);
            $this->em->flush();
            $res = true;
        }
        return $res;
    }
}