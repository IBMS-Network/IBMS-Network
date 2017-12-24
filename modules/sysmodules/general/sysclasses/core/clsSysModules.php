<?php

namespace engine\modules\general;

use engine\clsSysDB;

class clsSysModules
{
    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
    protected $em;

    /**
     * @var self
     */
    private static $instance;

    /**
     * Get class instance in the static context
     * @return clsSysModules
     */
    public static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new clsSysModules();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->em = clsSysDB::getInstance();
    }

    /**
     * Fetch modules by a set of criteria.
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
        $moduleRep = $this->em->getRepository('entities\Module');
        return $moduleRep->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Method to get module entity by name
     * @param string $name
     * @return \entities\Module|null
     */
    public function getModuleByName($name)
    {
        $modules = $this->fetchAll(['name' => $name], null, 1);
        if (!empty($modules[0])) {
            return $modules[0];
        }
        return null;
    }

}