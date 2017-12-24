<?php

namespace classes;

use classes\core\clsDB;

class clsProductsNew {

    static private $instance = NULL;
    private $em = NULL;
    
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsProductsNew();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->em = clsDB::getInstance();
    }
    
    /**
     * Get products for main page
     * 
     * @param integer $limit
     * @param integer $offset
     * @return array
     */
    public function getProductsForMain($limit = MAIN_BLOCKS_PRODUCTS_DEFAULT_LIMIT, $offset = null)
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('p') 
            ->from('entities\ProductsNew', 'ph')
            ->join('entities\Product', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.id = ph.productId')
            ->orderBy('p.id', 'DESC')
            ->where('p.status = 1')
            ;
        
        if(!empty($limit)) {
            $qb->setMaxResults((int)$limit);
        }
        if(!empty($offset)) {
            $qb->setFirstResult((int)$offset);
        }
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * Get products count for main page
     * 
     * @return integer
     */
    public function getProductsCount()
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('COUNT(p.id) cnt') 
            ->from('entities\ProductsNew', 'ph')
            ->join('entities\Product', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.id = ph.productId')
            ->where('p.status = 1')
            ;
        
        $result = $qb->getQuery()->getArrayResult();
        return $result[0][cnt] ? (int)$result[0][cnt] : 0;
    }
}