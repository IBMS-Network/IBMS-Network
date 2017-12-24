<?php

namespace classes;

use classes\core\clsDB;
use entities\News;
use Doctrine\ORM\Query\ResultSetMapping;

class clsNews {

    static private $instance = NULL;
    private $db = "";
    
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsNews();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->em = clsDB::getInstance();
    }

    /**
     * Get all news items or limited by month separated by page 
     * 
     * @param integer $page
     * @param string $month
     * @return array
     */
    public function fetchAll($page = 1, $month = '') {
        
        $sql = "SELECT n FROM entities\News n";
        if(!empty($month)) {
            $sql .= " WHERE DATE_FORMAT(n.created, '%m-%Y') = '" . $month . "'";
        }
        $sql .=  " ORDER BY n.created DESC ";
        $query = $this->em->createQuery($sql)
                    ->setMaxResults(NEWS_LIMIT)
                    ->setFirstResult(($page-1)*NEWS_LIMIT);
        return $query->getResult();
    }
    
    /**
     * Get count of all news items or limited by month news items
     * 
     * @param string $month
     * @return integer
     */
    public function getNewsCount($month = '') {
        $sql = "SELECT COUNT(n.id) FROM entities\News n";
        if(!empty($month)) {
            $sql .= " WHERE DATE_FORMAT(n.created, '%m-%Y') = '" . $month . "'";
        }
        $query = $this->em->createQuery($sql);
        return $query->getSingleScalarResult();
    }
    
    /**
     * Get news item by slug
     * 
     * @param string $slug
     * @return entities\News
     */
    public function getNewsItemBySlug($slug) {
        return $this->em->getRepository('entities\News')->findOneBySlug($slug);
    }
    
    /**
     * Get news item by news ID
     * 
     * @param string $id
     * @return entities\News
     */
    public function getNewsItemById($id) {
        return $this->em->getRepository('entities\News')->findOneById((int)$id);
    }
    
    /**
     * Get last news items (sort by created date)
     * 
     * @param integer $limit
     * @return array
     */
    public function getLastNews($limit = NEWS_LAST_BLOCK_LIMIT) {
        return $this->em->getRepository('entities\News')->findBy(array(), array('created' => 'DESC'), $limit);
    }
    
    /**
     * Get monthes when news items were created
     * 
     * @return array
     */
    public function getNewsMothtes() {

        $query = $this->em->createQuery("SELECT DISTINCT(DATE_FORMAT(n.created, '%m-%Y')) as created FROM entities\News n HAVING created != '00-0000' ORDER BY n.created DESC");

        return $query->getResult();
    }
}