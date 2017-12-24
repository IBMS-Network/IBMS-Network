<?php

namespace classes;

use classes\core\clsDB;

class clsSearch
{

    static private $instance = NULL;
    private $db = "";

    public static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new clsSearch();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->em = clsDB::getInstance();
    }
    
    /**
     * Get search results for query
     * 
     * @param string $search
     * @param integer $page
     * @return array|null
     */
    public function searchProducts($search, $limit = SEARCH_LIMIT, $offset = 0)
    {
        $qb = $this->em->createQueryBuilder();
        $query = $qb
            ->select('p')
            ->from('entities\Product p')
            ->join('p.brand', 'b')
            ->where('p.name LIKE :query')
            ->orWhere('p.description LIKE :query')
            ->orWhere('p.articul LIKE :query')
            ->orWhere('b.name LIKE :query')
            ->andWhere('p.status = 1')
            ->setParameter('query', '%' . $search . '%')
            ->setMaxResults(SEARCH_LIMIT)
            ->setFirstResult($offset)
            ->getQuery();

        return $query->getResult();
    }
    
    /**
     * Get search products count for query
     * 
     * @param integer $search
     * @return integer
     */
    public function searchProductsCount($search)
    {
        $qb = $this->em->createQueryBuilder();
        $query = $qb
            ->select('COUNT(DISTINCT p.id) cnt')
            ->from('entities\Product p')
            ->join('p.brand', 'b')
            ->where('p.name LIKE :query')
            ->orWhere('p.description LIKE :query')
            ->orWhere('p.articul LIKE :query')
            ->orWhere('b.name LIKE :query')
            ->andWhere('p.status = 1')
            ->setParameter('query', '%' . $search . '%')
            ->getQuery();

        $result = $qb->getQuery()->getArrayResult();
        return $result[0]['cnt'] ? (int)$result[0]['cnt'] : 0;
    }

}
