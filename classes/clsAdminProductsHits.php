<?php
namespace classes;

use classes\core\clsCommon;
use classes\core\clsDB;
use engine\clsAdminEntity;
use engine\modules\admin\clsAdminCommon;
use entities\ProductsHits;
use entities\Product;

/**
 * Prepare CRUD methods for working under ORM class \entities\ProductsHits
 * @author Anatoly.Bogdanov
 *
 */
class clsAdminProductsHits extends clsAdminEntity
{

    /**
     * Self object
     * @var clsAdminProductsHits $instance
     */
    private static $instance = null;

    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
    private $em;

    /**
     * Singleton
     * @return NULL|\classes\clsAdminProductsHits
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new clsAdminProductsHits();
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
        $this->entity = clsAdminCommon::getAdminMessage('product_hits', ADMIN_ENTITIES_BLOCK);
        $this->em = clsDB::getInstance();
    }

    /**
     * Get hits list
     * @param int $page
     * @param int $limit
     * @param string $sort
     * @param string $sorter
     * @param array $filter
     * @return array
     */
    public function getProductsHitsList($page = 1, $limit = DEF_PAGING_NUM, $sort = '', $sorter = 'desc', $filter = array())
    {
        $db = $this->em->createQueryBuilder();
        $db->select('ph')->from('entities\ProductsHits', 'ph');
        $whereClause = $this->getElmFilter($filter, 'entities\ProductsHits', 'ph', array('product'));
        if (!empty($whereClause)) {
            $db->where($whereClause);
        }
        if (!empty($sort) && in_array($sorter, array('asc', 'desc'))) {
            $db->orderBy('ph.'.$sort , $sorter);
        }
        $db->setFirstResult(((int)$page - 1) * $limit);
        $db->setMaxResults((int)$limit);

        return $db->getQuery()->getResult();
    }

    /**
     * Get count of the hits list
     */
    public function getProductsHitsListCount($filter = array())
    {
        $whereClause = $this->getElmFilter($filter, 'entities\ProductsHits', 'ph', array('product'));
        if (!empty($whereClause)) {
            $whereClause = " WHERE " . $whereClause;
        }
        $query = $this->em->createQuery("SELECT COUNT(ph) FROM entities\ProductsHits ph ".$whereClause);
        return $query->getSingleScalarResult();
    }

    /**
     * Get hit by ID
     *
     * @param integer $id
     *
     * @return FALSE | \Doctrine\ORM\EntityRepository
     */
    public function getProductsHitsById($id)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if ($id > 0) {
            $res = $this->em->getRepository('entities\ProductsHits')->find($id);
        }
        return $res;
    }

    /**
     * Add product hit
     * @param int $id
     * product id
     * @param string $articul
     * articul number
     * @return bool
     */
    public function addProductsHits($id = 0, $articul = '')
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if (!empty($id) || !empty($articul)) {
            $_prod = $this->em->getRepository('entities\Product')->find(array('id' => $id));
            $_prod2 = $this->em->getRepository('entities\Product')->findOneBy(array('articul' => $articul));
            if (!empty($_prod) || !empty($_prod2)) {
                $prod = new ProductsHits();
                if($_prod){
                    $prod->setProduct($_prod);
                } else {
                    $prod->setProduct($_prod2);
                    $id = $_prod2->getId();
                }

                $this->em->persist($prod);
                $this->em->flush();
                $res = true;
            } else {
                $error = clsAdminCommon::getAdminMessage(
                    'error_product_not_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entityid}' => $id, '{%articul}' => $articul)
                );
                $this->errors->setError($error, 1, false, true);
            }
        }
        return $id;
    }

    /**
     * delete product hit
     * @param int $id
     * ID of the product hit
     * @return boolean
     */
    public function deleteProductsHits($id)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if ( $id > 0) {
            $prod = $this->em->getRepository('entities\ProductsHits')->find($id);
            $this->em->remove($prod);
            $this->em->flush();
            $res = true;
        }
        return $res;
    }
}