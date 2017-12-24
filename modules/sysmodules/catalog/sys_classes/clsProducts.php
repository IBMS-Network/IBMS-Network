<?php

namespace engine\modules\catalog;

use engine\clsSysDB;
use entities\Product;
use classes\core\clsCommon;

/**
 * Class to work with products
 */
class clsProducts
{
    /**
     * @var self
     */
    public static $instance;

    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
    protected $em;

    /**
     * Get class instance in the static context
     * @return self
     */
    public static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new clsProducts();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->em = clsSysDB::getInstance();
    }

    /**
     * Fetch fields by a set of criteria.
     *
     * @param array $criteria Associative array where key as table field and value as field value
     * @param array|null $orderBy Associative array where key as table field and value as order type (ASC|DESC)
     * @param int|null $limit
     * @param int|null $offset
     * @param int $productId
     *
     * @return array The objects.
     */
    public function fetchAll(array $criteria = array(), array $orderBy = null, $limit = null, $offset = null, $productId = 0)
    {
        $fieldsRep = $this->em->getRepository('entities\Product');
        $criteria['status'] = 1;
        $query = $this->em->createQueryBuilder()
                ->select('p')
                ->from('entities\Product', 'p')
                ->leftJoin('entities\Image', 'i', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.id = i.item_id')
                ->where('p.id = ?1')
                ->andWhere('i')
                ->setParameter(1, $productId);
        
        return $fieldsRep->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Method to get product data by id
     * @param int $productId
     * @return Product|null Product entity instance or NULL if the entity can not be found.
     */
    public function getProduct($productId)
    {
        $productId = clsCommon::isInt($productId);
        return $this->em->find('entities\Product', $productId);
    }
    
    /**
     * Method to get products data by ids
     * @param array $products
     * @param boolean $aviable
     * @return array Product|null Product entities instance or NULL if the entity can not be found.
     */
    public function getProductsByIds($products, bool $aviable = null)
    {
        $criteria = array('id' => $products);
        if($aviable) {
            $criteria['availability'] = 1;
        }
        return $this->em->getRepository('entities\Product')->findBy($criteria);
    }
    
    /**
     * Method to get products data by category id
     * @param int|array $catIds
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @return array Product|null Product entity instance or NULL if the entity can not be found.
     */
    public function getProductsByCategoryIds($catIds, array $criteria = null, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('p') 
            ->from('entities\Product', 'p')
            ->join('p.categories', 'c')
            ->where('p.status = 1')
            ;
        
        if(!empty($catIds) && is_array($catIds)) {
            $qb->andWhere($qb->expr()->in('c.id', $catIds));
        }
        
        if(!empty($criteria)) {
            foreach($criteria as $k => $v) {
                switch($k) {
                    case 'priceto':
                        $qb->andWhere('p.price <= ' . (float)$v);
                        break;
                    case 'pricefrom':
                        $qb->andWhere('p.price >= ' . (float)$v);
                        break;
                    case 'color':
                        $qb->join('p.colors', 'col');
                        $qb->andWhere('col.id = ' . (int)$v);
                        break;
                    case 'texture':
                        $qb->join('p.texture', 't');
                        $qb->andWhere('t.id = ' . (int)$v);
                        break;
                    case 'brand':
                        $qb->join('p.brand', 'b');
                        $qb->andWhere('b.id = ' . (int)$v);
                        break;
                    case 'type':
                        switch($v) {
                            case 'hits':
                                $qb->join('entities\ProductsHits', 'ph', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.id = ph.productId');
                                break;
                            case 'new':
                                $qb->join('entities\ProductsNew', 'pn', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.id = pn.productId');
                                break;
                            case 'share':
                                $qb->andWhere('p.price2 > 0');
                                break;
                            default:
                                break;
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        
        if(!empty($orderBy)) {
            foreach($orderBy as $k => $v) {
                $qb->addOrderBy('p.' . $k, $v);
            }
        }
        if(!empty($limit)) {
            $qb->setMaxResults((int)$limit);
        }
        if(!empty($offset)) {
            $qb->setFirstResult((int)$offset);
        }
        return $qb->getQuery()->useQueryCache(true, 3600, 'category_product_' . join('_', $catIds))->getResult();
//        return $qb->getQuery()->getResult();
    }
    
    /**
     * Method to get products data by category id
     * @param array $catIds
     * @param array $criteria
     * 
     * @return int Product entity count or 0 if the no entity can not be found.
     */
    public function getProductsCount($catIds, array $criteria = null)
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('COUNT(p.id) cnt') 
            ->from('entities\Product', 'p')
            ->join('p.categories', 'c')
            ->where('p.status = 1')
            ;
        
        if(!empty($catIds) && is_array($catIds)) {
            $qb->andWhere($qb->expr()->in('c.id', $catIds));
        }
        
        if(!empty($criteria)) {
            foreach($criteria as $k => $v) {
                switch($k) {
                    case 'priceto':
                        $qb->andWhere('p.price <= ' . (float)$v);
                        break;
                    case 'pricefrom':
                        $qb->andWhere('p.price >= ' . (float)$v);
                        break;
                    case 'color':
                        $qb->join('p.colors', 'col');
                        $qb->andWhere('col.id = ' . (int)$v);
                        break;
                    case 'texture':
                        $qb->join('p.texture', 't');
                        $qb->andWhere('t.id = ' . (int)$v);
                        break;
                    case 'brand':
                        $qb->join('p.brand', 'b');
                        $qb->andWhere('b.id = ' . (int)$v);
                        break;
                    case 'type':
                        switch($v) {
                            case 'hits':
                                $qb->join('entities\ProductsHits', 'ph', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.id = ph.productId');
                                break;
                            case 'new':
                                $qb->join('entities\ProductsNew', 'pn', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.id = pn.productId');
                                break;
                            case 'share':
                                $qb->andWhere('p.price2 > 0');
                                break;
                            default:
                                break;
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        
        $result = $qb->getQuery()->getArrayResult();
        return $result[0]['cnt'] ? (int)$result[0]['cnt'] : 0;
    }
    
    /**
     * Get count of all products
     * 
     * @return array
     */
    public function getCountAll()
    {
        $query = $this->em->createQuery('SELECT COUNT(p.id) FROM entities\Product p');
        
        return $query->getSingleScalarResult();
    }
    
    /**
     * Get min and max price for products in categories
     * 
     * @param array $catIds
     * @return array
     */
    public function getMinMaxProductsCategory($catIds = array())
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('MAX(p.price) as maxPrice, MIN(p.price) as minPrice')
            ->from('entities\Product', 'p')
            ->where('p.status = 1')
            ;
        
        if(!empty($catIds) && is_array($catIds)) {
            $qb->join('p.categories', 'c');
            $qb->andWhere($qb->expr()->in('c.id', $catIds));
        }
        
        return $qb->getQuery()->getSingleResult();
    }
    
    /**
     * Get products for footer hot block
     * 
     * @return array
     */
    public function getProductsForHotBlock()
    {
        return $this->em->getRepository('entities\Product')->findBy(array(), array('created' => 'ASC'), 12, 0);
    }
    
    /**
     * Get products for main page and footer discount block
     * 
     * @param int $offset
     * @return array
     */
    public function getProductsForDiscountBlock($limit = MAIN_BLOCKS_PRODUCTS_DEFAULT_LIMIT, $offset = null)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('p')
            ->from('entities\Product', 'p')
            ->where('p.status = 1')
            ->andWhere('p.price2 > 0')
            ->orderBy('p.created', 'DESC')
            ->setMaxResults($limit);
        
        if(!empty($offset)) {
            $qb->setFirstResult($offset);
        }
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * Get product count for main page discount block
     * 
     * @return integer
     */
    public function getProductsCountForDiscountBlock()
    {
        $qb = $this->em->createQueryBuilder();
        $query = $qb
            ->select('COUNT(p.id) cnt')
            ->from('entities\Product', 'p')
            ->where('p.status = 1')
            ->andWhere('p.price2 > 0')
            ->getQuery();
        
        $result = $qb->getQuery()->getArrayResult();
        return $result[0][cnt] ? (int)$result[0][cnt] : 0;
    }
    
    /**
     * Get products for new products block
     * 
     * @return array
     */
    public function getProductsForNewBlock()
    {
        return $this->em->getRepository('entities\Product')->findBy(array(), array('created' => 'DESC'), 8, 0);
    }
}