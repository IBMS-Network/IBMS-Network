<?php

namespace classes;

use classes\core\clsDB;

class clsTextures {

    static private $instance = NULL;
    private $em = NULL;
    
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsTextures();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->em = clsDB::getInstance();
    }
    
    /**
     * Get textures for products in categories
     * 
     * @param array $ids
     * @return array
     */
    public function getTexturesForCategories($ids)
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select(array('t.id', 't.name')) 
            ->from('entities\Product', 'p')
            ->join('p.texture', 't')
            ->join('p.categories', 'c')
            ->orderBy('t.id', 'ASC')
            ->groupBy('t.id')
//            ->where('p.status = 1')
            ->andWhere($qb->expr()->in('c.id', $ids));
        
        return $qb->getQuery()->getResult();
    }
}