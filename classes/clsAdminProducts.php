<?php
namespace classes;

use classes\core\clsCommon;
use classes\core\clsDB;
use engine\clsAdminEntity;
use engine\modules\admin\clsAdminCommon;
use entities\Product as ProjProduct;

/**
 * Prepare CRUD methods for working under ORM class \entities\Product
 * @author Anatoly.Bogdanov
 *
 */
class clsAdminProducts extends clsAdminEntity
{

    /**
     * self object
     * @var clsAdminProducts
     */
    private static $instance = null;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * Singleton
     * @return NULL|\classes\clsAdminProducts
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new clsAdminProducts();
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
        $this->em = clsDB::getInstance();
        $this->entity = clsAdminCommon::getAdminMessage('product', ADMIN_ENTITIES_BLOCK);
    }

    /**
     * Get products list
     * @param int $page
     * @param int $limit
     * @param string $sort
     * @param string $sorter
     * @param array $filter
     * @return array
     */
    public function getProductsList($page = 1, $limit = DEF_PAGING_NUM, $sort = '', $sorter = 'desc', $filter = array())
    {
        $db = $this->em->createQueryBuilder();
        $db->select('pr')->from('entities\Product', 'pr');
        if(in_array('category', array_keys($filter))) {
            $db->join('pr.categories', 'cats');
            $filter['categories'] = $filter['category'];
            unset($filter['category']);
        }
        $whereClause = $this->getElmFilter($filter, 'entities\Product', 'pr', array('category', 'brand'));
        if (!empty($whereClause)) {
            $db->where($whereClause);
        }
        if (!empty($sort) && in_array($sorter, array('asc', 'desc'))) {
            $db->orderBy('pr.' . $sort, $sorter);
        }
        $db->setFirstResult(((int)$page - 1) * $limit);
        $db->setMaxResults((int)$limit);

        return $db->getQuery()->getResult();
    }

    /**
     * Get count of the products list
     * @param array $filter
     * @return mixed
     */
    public function getProductsListCount($filter = array())
    {
        $joinClause = '';
        if(in_array('category', array_keys($filter))) {
            $joinClause = ' INNER JOIN pr.categories cats';
            $filter['categories'] = $filter['category'];
            unset($filter['category']);
        }
        $whereClause = $this->getElmFilter($filter, 'entities\Product', 'pr', array('categories', 'brand'));
        if (!empty($whereClause)) {
            $whereClause = " WHERE " . $whereClause;
        }
        
        $query = $this->em->createQuery("SELECT COUNT(pr) FROM entities\Product pr" . $joinClause . $whereClause);
        return $query->getSingleScalarResult();
    }

    /**
     * Get Product data by ID
     * @param int $id
     * ID of the Product
     * @return boolean | \entities\Product
     */
    public function getProductById($id)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if ($id > 0) {
            $res = $this->em->getRepository('\entities\Product')->find($id);
        }
        return $res;
    }


    /**
     * Get Product by name and articul
     * @param string $name
     * product name
     * @param string $articul
     * product articul
     * @return bool|null|\entities\Product
     */
    public function getProductByNameAndArticul($name, $articul)
    {
        $res = false;
        if (!empty($name) && !empty($articul)) {
            $res = $this->em->getRepository('\entities\Product')->findOneBy(array('name'=>$name, 'articul' => $articul));
        }
        return $res;
    }

    /**
     * Get Product by articul
     * @param string $articul
     * product articul
     * @return bool|null|\entities\Product
     */
    public function getProductByArticul($articul)
    {
        $res = false;
        if (!empty($articul)) {
            $res = $this->em->getRepository('\entities\Product')->findOneBy(array('articul' => $articul));
        }
        return $res;
    }

    /**
     * Update product
     * @param int $id
     * Id
     * @param string $name
     * name
     * @param array $categories
     * category ids
     * @param string $desc
     * description
     * @param string $cont
     * content
     * @param string $art
     * articul
     * @param string $code
     * code
     * @param float $price
     * price
     * @param float $price2
     * price2
     * @param int $model_id
     * model id
     * @param int $brand_id
     * brand id
     * @param int $counry_id
     * country id
     * @param int $availability
     * availability id
     * @param int $texture_id
     * texture id
     * @param array $colors
     * colors ids
     * @param string $img
     * path to image
     * @param bool $status
     * status
     * @param array $similars
     * similar products
     * @return bool
     */
    public function updateProduct(
        $id,
        $name,
        array $categories,
        $desc,
        $cont,
        $art,
        $code,
        $price,
        $price2,
        $model_id = 0,
        $brand_id,
        $counry_id = 0,
        $availability,
        $texture_id,
        array $colors,
        $img = '',
        $status = true,
        $similars
    ) {
        $res = false;
        if ((int)$id > 0 && !empty($name) && !empty($art) && !empty($price) && !empty($categories) && !empty($availability) && (int)$brand_id > 0
        ) {
            $categoriesEntities = $this->em->getRepository('entities\Category')->findById($categories);
            $brand = $this->em->getRepository('entities\Brand')->find((int)$brand_id);
//            $model = $this->em->getRepository('entities\Model')->find((int)$model_id);
            $texture = $this->em->getRepository('entities\Texture')->find((int)$texture_id);
            $colors = $this->em->getRepository('entities\Color')->findById($colors);
            $db = $this->em->createQueryBuilder();
            $db->select('pr')->from('entities\Product', 'pr')
                ->where('pr.id != :identifier')->setParameter('identifier', clsCommon::isInt($id))
                ->andWhere('pr.name = :name')->setParameter('name', $name)
//                ->andWhere('pr.category = :category')->setParameter('category', $category)
//                ->andWhere('pr.model = :model')->setParameter('model', $model)
                ->andWhere('pr.brand = :brand')->setParameter('brand', $brand);;
            $_product = $db->getQuery()->getResult();
            if ($_product) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $name)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                $product = $this->em->getRepository('\entities\Product')->find(clsCommon::isInt($id));
                $product->setName($name)
                    ->setCategories($categoriesEntities)
                    ->setDescription($desc)
                    ->setArticul($art)
                    ->setAvailability((int)$availability)
                    ->setCode($code)
                    ->setPrice($price)
                    ->setPrice2($price2)
                    ->setModel($model)
                    ->setBrand($brand)
                    ->setColors($colors)
                    ->setTexture($texture)
                    ->setContent($cont)
                    ->setStatus($status);
                if (!empty($img)) {
                    $product->setImg($img);
                }
                if ((int)$counry_id > 0) {
                    $country = $this->em->getRepository('entities\Country')->find((int)$counry_id);
                    $product->setCountry($country);
                };
                if ((int)$model_id > 0) {
                    $model = $this->em->getRepository('entities\Model')->find((int)$model_id);
                    $product->setModel($model);
                }
                $product->clearSimilars();
                if(!empty($similars)) {
                    foreach($similars as $v) {
                        if (!empty($v['id']) || !empty($v['articul'])) {
                            $_prod = $this->em->getRepository('entities\Product')->find(array('id' => $v['id']));
                            $_prod2 = $this->em->getRepository('entities\Product')->findOneBy(array('articul' => $v['articul']));
                            if (!empty($_prod) || !empty($_prod2)) {
                                if($_prod){
                                    $product->setSimilar($_prod);
                                } else {
                                    $product->setSimilar($_prod2);
                                }

                            } else {
                                $error = clsAdminCommon::getAdminMessage(
                                    'error_product_not_exists',
                                    ADMIN_ERROR_BLOCK,
                                    array('{%entityid}' => $id, '{%articul}' => $articul)
                                );
                                $this->errors->setError($error, 1, false, true);
                            }
                        }
                    }
                }
                $this->em->persist($product);
                $this->em->flush();
                $res = $product;
            }
        }
        return $res;
    }

    /**
     * Delete Product
     * @param int $id
     * identificator of the Product
     * @return boolean
     */
    public function deleteProduct($id)
    {
        $res = false;
        if (clsCommon::isInt($id) > 0) {
            $product = $this->em->getRepository('\entities\AclProduct')->find(clsCommon::isInt($id));
            $this->em->remove($product);
            $this->em->flush();
            $res = true;
        }
        return $res;
    }

    /**
     * Add Product
     * @param string $name
     * name
     * @param array $categories
     * category ids
     * @param string $desc
     * description
     * @param string $cont
     * content
     * @param string $art
     * articul
     * @param string $code
     * code
     * @param float $price
     * price
     * @param float $price2
     * price2
     * @param int $model_id
     * model id
     * @param int $brand_id
     * brand id
     * @param int $counry_id
     * country id
     * @param int $availability
     * availability id
     * @param int $texture_id
     * texture id
     * @param array $colors
     * colors ids
     * @param string $img
     * path to image
     * @param bool $status
     * status
     * @param array $similars
     * similar products
     * @return boolean | integer
     */
    public function addProduct(
        $name,
        array $categories,
        $desc,
        $cont,
        $art,
        $code,
        $price,
        $price2,
        $model_id = 0,
        $brand_id,
        $counry_id = 0,
        $availability,
        $texture_id,
        array $colors,
        $img = '',
        $status = true,
        array $similars
    ) {
        if (!empty($name) && !empty($art) && !empty($price) && !empty($categories) && (int)$brand_id > 0 && !empty($availability)) {
            $categories = $this->em->getRepository('entities\Category')->findById($categories);
            $brand = $this->em->getRepository('entities\Brand')->find((int)$brand_id);
            $texture = $this->em->getRepository('entities\Texture')->find((int)$texture_id);
            $colors = $this->em->getRepository('entities\Color')->findById($colors);
            
            $db = $this->em->createQueryBuilder();
            $db->select('pr')->from('entities\Product', 'pr')
                ->where('pr.name = :name')->setParameter('name', $name)
//                ->andWhere('pr.category = :category')->setParameter('category', $category)
//                ->andWhere('pr.model = :model')->setParameter('model', $model)
                ->andWhere('pr.brand = :brand')->setParameter('brand', $brand);
            $_product = $db->getQuery()->getResult();
            if ($_product) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $name)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                $product = new ProjProduct();
                $product->setName($name)
                    ->setCategories($categories)
                    ->setDescription($desc)
                    ->setArticul($art)
                    ->setCode($code)
                    ->setPrice($price)
                    ->setPrice2($price2)
                    ->setBrand($brand)
                    ->setContent($cont)
                    ->setStatus($status)
                    ->setAvailability((int)$availability)
                    ->setColors($colors)
                    ->setTexture($texture)
                    ->setCreateDate();
                if (!empty($img)) {
                    $product->setImg($img);
                }
                if ((int)$counry_id > 0) {
                    $country = $this->em->getRepository('entities\Country')->find((int)$counry_id);
                    $product->setCountry($country);
                }
                if ((int)$model_id > 0) {
                    $model = $this->em->getRepository('entities\Model')->find((int)$model_id);
                    $product->setModel($model);
                }
                if(!empty($similars)) {
                    foreach($similars as $v) {
                        if (!empty($v['id']) || !empty($v['articul'])) {
                            $_prod = $this->em->getRepository('entities\Product')->find(array('id' => $v['id']));
                            $_prod2 = $this->em->getRepository('entities\Product')->findOneBy(array('articul' => $v['articul']));
                            if (!empty($_prod) || !empty($_prod2)) {
                                if($_prod){
                                    $product->setSimilar($_prod);
                                } else {
                                    $product->setSimilar($_prod2);
                                }
                            } else {
                                $error = clsAdminCommon::getAdminMessage(
                                    'error_product_not_exists',
                                    ADMIN_ERROR_BLOCK,
                                    array('{%entityid}' => $id, '{%articul}' => $articul)
                                );
                                $this->errors->setError($error, 1, false, true);
                            }
                        }
                    }
                }
                
                $this->em->persist($product);
                $this->em->flush();
                $res = $product;
            }
        }
        return $res;
    }
    
    /**
     * parse filters array to where string
     * @param array $filter
     * filter : key - field name, value - field value
     * @param $tableFields
     * existing table fields
     * @param $tName
     * alias of table
     * @param $intFields
     * that need an exact match
     * @param string $implode
     * AND | OR
     * @return string
     * where string to clause
     */
    protected function parseFilters($filter, $tableFields, $tName, $intFields, $implode = 'AND')
    {
        $arWhereClause = array();
        $whereClause = '';
        if (!empty($filter) && is_array($filter)) {
            foreach ($filter as $kFilter => $vFilter) {
                if (in_array($kFilter, $tableFields) && (trim($vFilter) != '' || is_null($vFilter))) {
                    if (is_null($vFilter)) {
                        $arWhereClause[] = $tName . $kFilter . " IS NULL ";
                    } else {
                        if($kFilter != 'categories') {
                            if (in_array($kFilter, $intFields)) {
                                $exprWhere = '=';
                                $exprValue = clsCommon::isInt($vFilter);
                            } else {
                                $exprWhere = 'LIKE';
                                $exprValue = '%' . $vFilter . '%';
                            }
                            $tNameNeed = $tName;
                        } else {
                            $exprWhere = '=';
                            $exprValue = clsCommon::isInt($vFilter);
                            $kFilter = 'cats.id';
                            $tNameNeed = '';
                        }
                        $arWhereClause[] = $tNameNeed . $kFilter . " " . $exprWhere . " '" . $exprValue . "'";
                    }
                }
            }
            $whereClause = implode(" " . $implode . " ", $arWhereClause);
        }
        return $whereClause;
    }

    public function setArticul($product, $newArticul){
        if(!empty($product) && $product instanceof ProjProduct){
            $product->setArticul($newArticul);
            $this->em->persist($product);
            $this->em->flush();
        }
    }
}