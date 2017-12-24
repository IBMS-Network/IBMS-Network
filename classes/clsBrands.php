<?php

namespace classes;

use classes\core\clsDB;

class clsBrands {

    static private $instance = NULL;
    private $em = NULL;
    
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsBrands();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->em = clsDB::getInstance();
    }
    
    /**
     * Get brands for products in categories
     * 
     * @param array $ids
     * @return array
     */
    public function getBrandsForCategories($ids)
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select(array('b.id', 'b.name')) 
            ->from('entities\Product', 'p')
            ->join('p.brand', 'b')
            ->join('p.categories', 'c')
            ->orderBy('b.id', 'ASC')
            ->groupBy('b.id')
//            ->where('p.status = 1')
            ->andWhere($qb->expr()->in('c.id', $ids));
        
        return $qb->getQuery()->getResult();
    }
}