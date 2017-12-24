<?php
namespace classes;

use classes\core\clsCommon;
use classes\core\clsDB;
use engine\clsAdminEntity;
use engine\modules\admin\clsAdminCommon;

/**
 * Prepare CRUD methods for working under ORM class \entities\News
 * @author Anatoly.Bogdanov
 *
 */
class clsAdminNews extends clsAdminEntity
{

    /**
     * Self object
     * @var clsAdminNews $instance
     */
    private static $instance = null;

    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
    private $em;

    /**
     * Constructor of the class.
     * Set entity name, get ORM Entity Manager object
     */
    public function __construct()
    {
        parent::__construct();
        $this->entity = clsAdminCommon::getAdminMessage('news', ADMIN_ENTITIES_BLOCK);
        $this->em = clsDB::getInstance();
    }

    /**
     * Singleton
     * @return NULL|\classes\clsAdminNews
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new clsAdminNews();
        }
        return self::$instance;
    }

    /**
     * Get news list
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
    public function getNewsList($page = 1, $limit = DEF_PAGING_NUM, $sort = '', $sorter = 'desc', $filter = array())
    {
        $db = $this->em->createQueryBuilder();
        $db->select('news')->from('entities\News', 'news');
        $whereClause = $this->getElmFilter($filter, 'entities\News', 'news', array('author'));
        if (!empty($whereClause)) {
            $db->where($whereClause);
        }
        if (!empty($sort) && in_array($sorter, array('asc', 'desc'))) {
            $db->orderBy('news.'.$sort , $sorter);
        }
        $db->setFirstResult(((int)$page - 1) * $limit);
        $db->setMaxResults((int)$limit);

        return $db->getQuery()->getResult();
    }

    /**
     * Get count of the news list
     * @param array $filter
     * array of filters, where key of element is name of filtering field
     * @return mixed
     */
    public function getNewsListCount($filter = array())
    {
        $whereClause = $this->getElmFilter($filter, 'entities\News', 'news', array('author'));
        if (!empty($whereClause)) {
            $whereClause = " WHERE " . $whereClause;
        }
        $query = $this->em->createQuery("SELECT COUNT(news) FROM entities\News news " . $whereClause);
        return $query->getSingleScalarResult();
    }

    /**
     * Get news by ID
     *
     * @param integer $id
     *
     * @return FALSE | \Doctrine\ORM\EntityRepository
     */
    public function getNewsById($id)
    {
        $res = false;
        if (clsCommon::isInt($id) > 0) {
            $res = $this->em->getRepository('entities\News')->find(clsCommon::isInt($id));
        }
        return $res;
    }

    /**
     * Add news
     * @param string $name
     * name
     * @param string $content
     * content
     * @param int $author_id
     * id creator
     * @param string $img
     * news image
     * @return bool
     */
    public function addNews($name, $content, $author_id, $img = '')
    {
        $res = false;
        if (!empty($name) && !empty($name) && (int)$author_id > 0) {

            $_news = $this->em->getRepository('entities\News')->findBy(array('name' => $name));
            if ($_news) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $name)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                $news = new \entities\News();
                $news->setName($name)
                    ->setText($content);
                if(!empty($img)){
                    $news->setImg($img);
                }
                $author = $this->em->getRepository('entities\Admin')->find(clsCommon::isInt($author_id));
                if($author){
                    $news->setAuthor($author);
                }

                $this->em->persist($news);
                $this->em->flush();
                $res = true;
            }
        }
        return $res;
    }

    /**
     * Update news
     * @param int $id
     * id
     * @param string $name
     * name
     * @param string $content
     * content
     * @param int $author_id
     * id creator
     * @param string $img
     * news image
     * @return boolean
     */
    public function updateNews($id, $name, $content, $author_id, $img = '')
    {
        $res = false;
        if ((int)$id > 0 && !empty($name) && (int)$author_id > 0) {
            $db = $this->em->createQueryBuilder();
            $db->select('news')->from('entities\News', 'news')
                ->where('news.id != :identifier')->setParameter('identifier', clsCommon::isInt($id))
                ->andWhere('news.name = :name')->setParameter('name', $name);
            $_news = $db->getQuery()->getResult();
            if ($_news) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $name)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                $news = $this->em->getRepository('entities\News')->find(clsCommon::isInt($id));
                $news->setName($name)
                    ->setText($content);
                if(!empty($img)){
                    $news->setImg($img);
                }
                $author = $this->em->getRepository('entities\Admin')->find(clsCommon::isInt($author_id));
                if($author){
                    $news->setAuthor($author);
                }

                $this->em->persist($news);
                $this->em->flush();
                $res = true;
            }
        }
        return $res;
    }

    /**
     * Delete news
     * @param int $id
     * identificator of the news
     * @return boolean
     */
    public function deleteNews($id)
    {
        $res = false;
        if (clsCommon::isInt($id) > 0) {
            $news = $this->em->getRepository('entities\News')->find(clsCommon::isInt($id));
            $this->em->remove($news);
            $this->em->flush();
            $res = true;
        }
        return $res;
    }
}