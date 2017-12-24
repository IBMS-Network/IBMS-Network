<?php

namespace engine\modules\seo;

use engine\clsSysDB;

/**
 * SEO module' class
 */
class clsSeo
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
     * @return clsSeo
     */
    public static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new clsSeo();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->em = clsSysDB::getInstance();
    }

    /**
     * @return \Doctrine\ORM\EntityManager|\engine\Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * Fetch all pages by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array The objects.
     */
    public function getPages(array $criteria = [], array $orderBy = null, $limit = null, $offset = null)
    {
        $pageRep = $this->em->getRepository('entities\Page');
        return $pageRep->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Method to get page by field
     * @param string $field
     * @param mixed $value
     * @return \entities\Page|null
     */
    public function getPageByField($field, $value)
    {
        return $this->em->getRepository('entities\Page')->findOneBy([$field => $value]);
    }

    /**
     * @param int $pageId
     * @return array The objects.
     */
    public function getPageFields($pageId)
    {
        $fieldsList = [];
        $fields = $this->em->getRepository('entities\SeoFieldValue')->findBy(['pageId' => $pageId]);
        foreach ($fields as $field){
            $fieldsList[$field->getFieldId()] = $field;
        }
        return $fieldsList;
    }

    /**
     * @param \entities\Page $page
     * @param array $fields
     */
    public function savePageFields(\entities\Page $page , array $fields)
    {
        $this->deletePageFields($page->getId());
        foreach ($fields as $fieldId => $fieldValue) {
            $seoFieldValue = new \entities\SeoFieldValue();
            $seoFieldValue->setPageId($page->getId());
            $seoFieldValue->setModuleId($page->getModule()->getId());
            $seoFieldValue->setFieldId($fieldId);
            $seoFieldValue->setValue($fieldValue);
            $this->em->persist($seoFieldValue);
        }
        $this->em->flush();
    }

    /**
     * @param int $pageId
     * @return int
     */
    public function deletePageFields($pageId)
    {
        return $this->em->createQuery('DELETE FROM entities\SeoFieldValue sfv WHERE sfv.pageId =' . $pageId)->execute();
    }
}