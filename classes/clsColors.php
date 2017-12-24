<?php

namespace classes;

use classes\core\clsDB;

class clsColors {

    static private $instance = NULL;
    private $em = NULL;
    
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsColors();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->em = clsDB::getInstance();
    }
    
    /**
     * Get colors for products in categories
     * 
     * @param array $ids
     * @return array
     */
    public function getColorsForCategories($ids)
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select(array('c.id', 'c.name')) 
            ->from('entities\Product', 'p')
            ->join('p.colors', 'c')
            ->join('p.categories', 'ct')
            ->orderBy('p.id', 'DESC')
            ->groupBy('c.id')
//            ->where('p.status = 1')
            ->andWhere($qb->expr()->in('ct.id', $ids));
        
        return $qb->getQuery()->getResult();
    }
}