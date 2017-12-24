<?php

namespace engine\modules\staticpages;

use engine\clsSysDB;
use engine\modules\general\clsSysModules;

class clsSysStaticPage
{
    const STATUS_SUCCESS = 1;
    const STATUS_FAIL = -1;
    const STATUS_PAGE_NOT_EXISTS = -2;
    const STATUS_PAGE_ALREADY_EXISTS = -3;

    /**
     * @var clsSysStaticPage
     */
    public static $instance;

    /**
     * @var \Doctrine\ORM\EntityManager $em ;
     */
    protected $em;

    public function __construct()
    {
        $this->em = clsSysDB::getInstance();
    }

    public static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new clsSysStaticPage();
        }
        return self::$instance;
    }

    /**
     * Finds static pages by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array The objects.
     */
    public function fetchAll(array $criteria = array(), array $orderBy = null, $limit = null, $offset = null)
    {
        $staticPageRep = $this->em->getRepository('entities\StaticPage');
        return $staticPageRep->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Method to get static page data by id
     * @param int $pageId
     * @return \entities\StaticPage|null The static page entity instance or NULL if the entity can not be found.
     */
    public function getPage($pageId)
    {
        $pageId = (int)$pageId;
        return $this->em->find('entities\StaticPage', $pageId);
    }

    /**
     * Method to add new static page entity to DB
     * @param \entities\StaticPage
     * @return int Last inserted id
     */
    public function addPage(\entities\StaticPage $staticPage)
    {
        $result = self::STATUS_SUCCESS;
        if (!$staticPage->getSlug()) {
            $result = self::STATUS_FAIL;
        } else {
            $existingStaticPage = $this->em->getRepository('entities\StaticPage')->findOneBy(array('slug' => $staticPage->getSlug()));
            if ($existingStaticPage) {
                $result =  self::STATUS_PAGE_ALREADY_EXISTS;
            } else {
                $module = clsSysModules::getInstance()->getModuleByName('static_pages');
                $page = new \entities\Page;
                $page->setName($staticPage->getTitle());
                $page->setModule($module);
                $staticPage->setPage($page);
                $result =  $this->savePage($staticPage);
            }
        }

        return $result;
    }

    /**
     * Method to update existing entity
     * @param \entities\StaticPage $page
     * @return int
     */
    public function updatePage(\entities\StaticPage $page)
    {
        $existingPage = $this->em->find('entities\StaticPage', $page->getId());
        if ($existingPage) {
            return $this->savePage($page);
        } else {
            return self::STATUS_PAGE_NOT_EXISTS;
        }
    }

    /**
     * Method to remove static page entity from DB by id
     * @param int $pageId
     * @return int Status of deleting
     */
    public function deletePage($pageId)
    {
        $pageId = (int)$pageId;
        $page = $this->em->find('entities\StaticPage', $pageId);
        if ($page) {
            $this->em->remove($page);
            $this->em->flush();
            return self::STATUS_SUCCESS;
        } else {
            return self::STATUS_PAGE_NOT_EXISTS;
        }
    }

    /**
     * Method to save static page entity into DB
     * @param \entities\StaticPage $page
     * @return int Last inserted id or status of error
     */
    protected function savePage(\entities\StaticPage $page)
    {
        $this->em->persist($page);
        $this->em->flush();
        return $page->getId();
    }
    
     /**
     * Method to get static page entities from DB to show in static page menu
     * @return array The objects.
     */
    public function getForMenu()
    {
        $staticPageRep = $this->em->getRepository('entities\StaticPage');
        return $staticPageRep->findBy(array('showMenu' => true));
    }
}