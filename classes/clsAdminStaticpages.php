<?php
namespace classes;

use classes\core\clsCommon;
use classes\core\clsDB;
use engine\clsAdminEntity;
use engine\modules\admin\clsAdminCommon;

/**
 * Prepare CRUD methods for working under ORM class \entities\StaticPage
 * @author Anatoly.Bogdanov
 *
 */
class clsAdminStaticpages extends clsAdminEntity
{

    /**
     * Self object
     * @var clsAdminStaticpages $instance
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
        $this->entity = clsAdminCommon::getAdminMessage('staticpage', ADMIN_ENTITIES_BLOCK);
        $this->em = clsDB::getInstance();
    }

    /**
     * Singleton
     * @return NULL|\classes\clsAdminStaticpages
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new clsAdminStaticpages();
        }
        return self::$instance;
    }

    /**
     * Get staticpage list
     * @param int $page
     * page number
     * @param int $limit
     * limit elements per page
     * @param string $sort
     * sort field name
     * @param string $sorter
     * 'asc' or 'desc'
     * @param array $filter
     * array of filters, where key of element is name of filtering field
     * @return array
     */
    public function getStaticpagesList($page = 1, $limit = DEF_PAGING_NUM, $sort = '', $sorter = 'desc', $filter = array())
    {
        $db = $this->em->createQueryBuilder();
        $db->select('stp')->from('entities\StaticPage', 'stp');
        $whereClause = $this->getElmFilter($filter, 'entities\StaticPage', 'stp', array('author'));
        if (!empty($whereClause)) {
            $db->where($whereClause);
        }
        if (!empty($sort) && in_array($sorter, array('asc', 'desc'))) {
            $db->orderBy('stp.'.$sort , $sorter);
        }
        $db->setFirstResult(((int)$page - 1) * $limit);
        $db->setMaxResults((int)$limit);

        return $db->getQuery()->getResult();
    }

    /**
     * Get count of the staticpage list
     * @param array $filter
     * array of filters, where key of element is name of filtering field
     * @return mixed
     */
    public function getStaticpagesListCount($filter = array())
    {
        $whereClause = $this->getElmFilter($filter, 'entities\StaticPage', 'stp', array('author'));
        if (!empty($whereClause)) {
            $whereClause = " WHERE " . $whereClause;
        }
        $query = $this->em->createQuery("SELECT COUNT(stp) FROM entities\StaticPage stp ".$whereClause);
        return $query->getSingleScalarResult();
    }

    /**
     * Get staticpage by ID
     *
     * @param integer $id
     *
     * @return FALSE | \Doctrine\ORM\EntityRepository
     */
    public function getStaticPageById($id)
    {
        $res = false;
        if (clsCommon::isInt($id) > 0) {
            $res = $this->em->getRepository('entities\StaticPage')->find(clsCommon::isInt($id));
        }
        return $res;
    }

    /**
     * Add static page
     * @param string $name
     * name
     * @param text $content
     * content
     * @param string $alias
     * url alias
     * @param int $author_id
     * id creator
     * @return bool
     */
    public function addStaticPage($name, $content, $alias, $author_id)
    {
        $res = false;
        if (!empty($name) && !empty($name) && !empty($alias) && (int)$author_id > 0) {

            $_stp = $this->em->getRepository('entities\StaticPage')->findBy(array('slug' => $alias));
            if ($_stp) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $alias)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                $stp = new \entities\StaticPage();
                $stp->setTitle($name)
                    ->setSlug($alias)
                    ->setContent($content)
                    ->setCreated()
                    ->setUpdated();
                $author = $this->em->getRepository('entities\Admin')->find(clsCommon::isInt($author_id));
                if($author){
                    $stp->setAuthor($author);
                }

                $this->em->persist($stp);
                $this->em->flush();
                $res = true;
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
     * @param text $content
     * content
     * @param string $alias
     * url alias
     * @param int $author_id
     * id creator
     * @return boolean
     */
    public function updateStaticPage($id, $name, $content, $alias, $author_id)
    {
        $res = false;
        if ((int)$id > 0 && !empty($name) && !empty($alias) && (int)$author_id > 0) {
            $db = $this->em->createQueryBuilder();
            $db->select('stp')->from('entities\StaticPage', 'stp')
                ->where('stp.id != :identifier')->setParameter('identifier', clsCommon::isInt($id))
                ->andWhere('stp.slug = :slug')->setParameter('slug', $alias);
            $_stp = $db->getQuery()->getResult();
            if ($_stp) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $name)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                $stp = $this->em->getRepository('entities\StaticPage')->find(clsCommon::isInt($id));
                $stp->setTitle($name)
                    ->setSlug($alias)
                    ->setContent($content)
                    ->setUpdated();
                $author = $this->em->getRepository('entities\Admin')->find(clsCommon::isInt($author_id));
                if($author){
                    $stp->setAuthor($author);
                }

                $this->em->persist($stp);
                $this->em->flush();
                $res = true;
            }
        }
        return $res;
    }

    /**
     * Delete staticpage
     * @param int $id
     * identificator of the staticpage
     * @return boolean
     */
    public function deleteStaticPage($id)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if ( $id > 0) {
            $stp = $this->em->getRepository('entities\StaticPage')->find($id);
            $this->em->remove($stp);
            $this->em->flush();
            $res = true;
        }
        return $res;
    }
}