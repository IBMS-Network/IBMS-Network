<?php

namespace engine\modules\catalog;

use engine\clsSysDB;
use entities\Category;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query;

/**
 * Class to work with categories
 */
class clsCategories
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
            self::$instance = new clsCategories();
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
     *
     * @return array The objects.
     */
    public function fetchAll(array $criteria = array(), array $orderBy = null, $limit = null, $offset = null)
    {
        $fieldsRep = $this->em->getRepository('entities\Category');
        return $fieldsRep->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * get category count
     */
    public function countCategory()
    {
        $query = $this->em->createQuery("SELECT COUNT(ca) FROM entities\Category ca");
        return $query->getSingleScalarResult();
    }

    /**
     * Method to get category data by id
     * @param int $categoryId
     * @return Category|null Category entity instance or NULL if the entity can not be found.
     */
    public function getCategory($categoryId)
    {
        $categoryId = (int)$categoryId;
        return $this->em->find('entities\Category', $categoryId);
    }

    /**
     * Method to add new category entity to DB
     * @param Category $category
     * @return int|bool Last inserted id
     */
    public function addCategory(Category $category)
    {
        $result = true;
        $existingCategory = $this->em->getRepository('entities\Category')->findOneBy(array('name' => $category->getName(), 'parent_id' => $category->getParentId()));
        if ($existingCategory) {
            /**
             * @todo Make message setting by Error class
             */
        } else {
            $result = $this->saveCategory($category);
        }

        return $result;
    }

    /**
     * Method to update existing entity
     * @param Category $category
     * @return int
     */
    public function updateCategory(Category $category)
    {
        $existingCategory = $this->em->find('entities\Category', $category->getId());
        if ($existingCategory) {
            return $this->saveCategory($category);
        } else {
            /**
             * @todo Make message setting by Error class
             */
        }
    }

    /**
     * Method to remove category entity from DB by id
     * @param int $categoryId
     * @return int Status of deleting
     */
    public function deleteCategory($categoryId)
    {
        $categoryId = (int)$categoryId;
        $category = $this->em->find('entities\Category', $categoryId);
        if ($category) {
            $this->em->remove($category);
            $this->em->flush();
            return 1;
        } else {
            /**
             * @todo Make message setting by Error class
             */
            return 0;
        }
    }

    /**
     * Method returns category statuses list
     * @return array
     */
    public function getStatusesList()
    {
        return array(1 => 'Active', 2 => 'Hidden');
    }

    /**
     * Method to save category entity into DB
     * @param Category $category
     * @return int Last inserted id or status of error
     */
    protected function saveCategory(Category $category)
    {
        $this->em->persist($category);
        $this->em->flush();
        return $category->getId();
    }

    public function getCategoryChildrensCount($categoryId)
    {
        $categoryId = (int)$categoryId;

        $query = $this->em->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from('entities\Category', 'c')
            ->where('c.parent_id = :id')
            ->setParameter('id', $categoryId)
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    public function getCategoryChildrens($categoryId)
    {
        $categoryId = (int)$categoryId;

        return $this->em->getRepository('entities\Category')->findBy(array('parent_id' => $categoryId));
    }

    public function getCategoriesForMenu()
    {
        $fieldsRep = $this->em->getRepository('entities\Category');
        return $fieldsRep->findBy(array('parent_id' => NULL), array('parent_id' => 'asc'));
    }

    public function getCategoriesForMenu2($id)
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('c.id', 'c.parent_id', 'c.name', 'COUNT(p.id) as cnt')
            ->from('entities\Category', 'c')
            ->join('c.products', 'p')
            ->groupBy('c.id')
            ->orderBy('c.parent_id', 'ASC')
            ->orderBy('c.id', 'ASC')
            ->where('c.status = 1');
        if(!empty($id)) {
            $qb->andWhere('c.parent_id = ' . (int)$id);
        }
        $query = $qb->getQuery();
        
        $result = $query->getArrayResult();
        
        
        foreach($result as $k => $v) {
            if($v['parent_id'] == NULL) {
                $resultSorted[$v['id']] = $v;
            } else {
                $resultSorted[$v['parent_id']]['children'][] = $v;
                $resultSorted[$v['parent_id']]['cnt'] += $v['cnt'];
            }
        }

        return $resultSorted;
    }
}