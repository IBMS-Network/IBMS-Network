<?php
namespace classes;

use classes\core\clsCommon;
use classes\core\clsDB;
use engine\clsAdminEntity;
use engine\modules\admin\clsAdminCommon;
use entities\Category;

/**
 * Prepare CRUD methods for working under ORM class \entities\Category
 * @author Anatoly.Bogdanov
 *
 */
class clsAdminCategories extends clsAdminEntity
{

    /**
     * Self object
     * @var clsAdminCategories $instance
     */
    private static $instance = null;

    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
    private $em;

    /**
     * Constructorof the class.
     * Set entity name, get ORM Entity Manager object
     */
    public function __construct()
    {
        parent::__construct();
        $this->entity = clsAdminCommon::getAdminMessage('category', ADMIN_ENTITIES_BLOCK);
        $this->em = clsDB::getInstance();
    }

    /**
     * Singleton
     * @return NULL|\classes\clsAdminCategories
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new clsAdminCategories();
        }
        return self::$instance;
    }

    /**
     * Get category list
     * @param int $page
     * page number
     * @param int $limit
     * limit elements per page
     * @param string $sort
     * @param string $sorter
     * @param array $filter
     * @return array
     */
    public function getCategoriesList($page = 1, $limit = DEF_PAGING_NUM, $sort = '', $sorter = 'desc', $filter = array())
    {
        $db = $this->em->createQueryBuilder();
        $db->select('c')->from('entities\Category', 'c');
        $whereClause = $this->getElmFilter($filter, 'entities\Category', 'c', array('parent'));
        if (!empty($whereClause)) {
            $db->where($whereClause);
        }
        if (!empty($sort) && in_array($sorter, array('asc', 'desc'))) {
            $db->orderBy('c.'.$sort , $sorter);
        }
        $db->setFirstResult(((int)$page - 1) * $limit);
        $db->setMaxResults((int)$limit);

        return $db->getQuery()->getResult();
    }

    /**
     * Get count of the category list
     * @param $filter
     * @return mixed
     */
    public function getCategoriesListCount($filter)
    {
        $whereClause = $this->getElmFilter($filter, 'entities\Category', 'c', array('parent'));
        if (!empty($whereClause)) {
            $whereClause = " WHERE " . $whereClause;
        }
        $query = $this->em->createQuery("SELECT COUNT(c) FROM entities\Category c ". $whereClause);
        return $query->getSingleScalarResult();
    }

    /**
     * Get category by ID
     *
     * @param integer $id
     *
     * @return FALSE | \Doctrine\ORM\EntityRepository
     */
    public function getCategoryById($id)
    {
        $res = false;
        if (clsCommon::isInt($id) > 0) {
            $res = $this->em->getRepository('entities\Category')->find(clsCommon::isInt($id));
        }
        return $res;
    }

    /**
     * Get category by Name and parent
     *
     * @param string $name
     * @param Category $parent
     *
     * @return FALSE | \Doctrine\ORM\EntityRepository
     */
    public function getCategoryByName($name, $parent = null)
    {
        $res = false;
        if (!empty($name)) {
            $condition = array('name'=>$name);
            if(!empty($parent) && $parent instanceof Category){
                $condition['parent'] = $parent;
            } else {
                $condition['parent'] = NULL;
            }
            $res = $this->em->getRepository('entities\Category')->findOneBy($condition);
        }
        return $res;
    }

    /**
     * @param string $name
     * name
     * @param int $parent_id
     * parent id
     * @param string $content
     * description
     * @param bool $status
     * status
     * @return bool
     */
    public function addCategory($name, $parent_id, $content="", $status = true)
    {
        $res = false;
        if (!empty($name)) {
            $parent = null;
            $parent_id = clsCommon::isInt($parent_id);
            if($parent_id > 0){
                $parent = $this->em->getRepository('entities\Category')->find($parent_id);
            }
            $db = $this->em->createQueryBuilder();
            $db->select('c')->from('entities\Category', 'c')
                ->where('c.parent = :parent')->setParameter('parent', $parent)
                ->andWhere('c.name = :name')->setParameter('name', $name);
            $_c = $db->getQuery()->getResult();
            if ($_c) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $name)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                $c = new Category();
                $c->setName($name)
                    ->setDescription($content)
                    ->setStatus($status)
                    ->setCreated()
                    ->setUpdated();
                if(!empty($parent) && $parent instanceof Category){
                    $c->setParent($parent);
                }

                $this->em->persist($c);
                $this->em->flush();
                $res = $c;
            }
        }
        return $res;
    }

    /**
     * Update static page
     * @param int $id
     * id
     * @param string $name
     * name
     * @param int $parent_id
     * parent id
     * @param string $content
     * description
     * @param bool $status
     * status
     * @return boolean
     */
    public function updateCategory($id, $name, $parent_id, $content="", $status = true)
    {
        $res = false;
        if ((int)$id > 0 && !empty($name)) {
            $parent = null;
            if((int)$parent_id > 0){
                $parent = $this->em->getRepository('entities\Category')->find(clsCommon::isInt($parent_id));
            }
            $db = $this->em->createQueryBuilder();
            $db->select('c')->from('entities\Category', 'c')
                ->where('c.id != :identifier')->setParameter('identifier', clsCommon::isInt($id))
                ->andWhere('c.parent = :parent')->setParameter('parent', $parent)
                ->andWhere('c.name = :name')->setParameter('name', $name);
            $_c = $db->getQuery()->getResult();
            if ($_c) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $name)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                $c = $this->em->getRepository('entities\Category')->find(clsCommon::isInt($id));
                $c->setName($name)
                    ->setDescription($content)
                    ->setParent($parent)
                    ->setStatus($status)
                    ->setCreated()
                    ->setUpdated();

                $this->em->persist($c);
                $this->em->flush();
                $res = $c;
            }
        }
        return $res;
    }

    /**
     * Delete category
     * @param int $id
     * ID of the category
     * @return boolean
     */
    public function deleteCategory($id)
    {
        $res = false;
        if (clsCommon::isInt($id) > 0) {
            $c = $this->em->getRepository('entities\Category')->find(clsCommon::isInt($id));
            $this->em->remove($c);
            $this->em->flush();
            $res = true;
        }
        return $res;
    }
}