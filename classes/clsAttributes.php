<?php
namespace classes;

use classes\core\clsDB;
use entities\Order;

class clsAttributes {

    static private $instance = NULL;
    private $db = "";
    
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsAttributes();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->em = clsDB::getInstance();
    }

    public function getProductAttributes($productId) {

        $qb = $this->em->createQueryBuilder();
        $query = $qb
            ->select('a')
            ->from('entities\Attribute a')
            ->leftJoin(
                'entities\ProductsAttributes',
                'pa',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'pa.attributeId = a.id'
            )
            ->where('pa.productId = :id')
//            ->orWhere('p.description LIKE :query')
//            ->orWhere('a.value LIKE :query')
//            ->andWhere('p.status = 1')
            ->setParameter('id', $productId)
//            ->setMaxResults(12)
            ->getQuery();

        return $query->getResult();
    }
}