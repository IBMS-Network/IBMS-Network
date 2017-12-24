<?php

namespace engine\modules\seo;

use engine\clsSysDB;

/**
 * Class to work with list of the SEO fields
 */
class clsSeoFields
{
    const STATUS_SUCCESS = 1;
    const STATUS_FAIL = -1;
    const STATUS_FIELD_NOT_EXISTS = -2;
    const STATUS_FIELD_ALREADY_EXISTS = -3;

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
     * @return clsSeoFields
     */
    public static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new clsSeoFields();
        }
        return self::$instance;
    }

    public static function validateFieldName($name){
        return (bool) preg_match('/^[a-zA-Z-]+$/', $name);
    }

    public function __construct()
    {
        $this->em = clsSysDB::getInstance();
    }

    /**
     * Fetch fields by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array The objects.
     */
    public function fetchAll(array $criteria = [], array $orderBy = null, $limit = null, $offset = null)
    {
        $fieldsRep = $this->em->getRepository('entities\SeoField');
        return $fieldsRep->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Method to get field data by id
     * @param int $fieldId
     * @return \entities\StaticPage|null Field entity instance or NULL if the entity can not be found.
     */
    public function getField($fieldId)
    {
        $fieldId = (int)$fieldId;
        return $this->em->find('entities\SeoField', $fieldId);
    }

    /**
     * Method to add new field entity to DB
     * @param \entities\SeoField $field
     * @return int Last inserted id
     */
    public function addField(\entities\SeoField $field)
    {
        if (!$field->getName()) {
            return self::STATUS_FAIL;
        }
        $existingField = $this->em->getRepository('entities\SeoField')->findOneBy(['name' => $field->getName()]);
        if ($existingField) {
            return self::STATUS_FIELD_ALREADY_EXISTS;
        } else {
            return $this->saveField($field);
        }
    }

    /**
     * Method to update existing entity
     * @param \entities\SeoField $field
     * @return int
     */
    public function updateField(\entities\SeoField $field)
    {
        $existingField = $this->em->find('entities\SeoField', $field->getId());
        if (!$existingField) {
            return self::STATUS_FIELD_NOT_EXISTS;
        } elseif (strtolower($existingField->getName()) !== strtolower($field->getName())) {
            if ($this->fetchAll(['name' => $existingField->getName()])) {
                return self::STATUS_FIELD_ALREADY_EXISTS;
            }
        }

        $this->saveField($field);
        return self::STATUS_SUCCESS;
    }

    /**
     * Method to remove field entity from DB by id
     * @param int $fieldId
     * @return int Status of deleting
     */
    public function deleteField($fieldId)
    {
        $fieldId = (int)$fieldId;
        $field = $this->em->find('entities\SeoField', $fieldId);
        if ($field) {
            $this->em->remove($field);
            $this->em->flush();
            return self::STATUS_SUCCESS;
        } else {
            return self::STATUS_FIELD_NOT_EXISTS;
        }
    }

    /**
     * Method to save field entity into DB
     * @param \entities\SeoField $field
     * @return int Last inserted id or status of error
     */
    protected function saveField(\entities\SeoField $field)
    {
        $this->em->persist($field);
        $this->em->flush();
        return $field->getId();
    }
}