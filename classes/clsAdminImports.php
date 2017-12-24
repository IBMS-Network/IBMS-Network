<?php
namespace classes;

use classes\core\clsCommon;
use classes\core\clsDB;
use engine\clsAdminEntity;
use engine\modules\admin\clsAdminCommon;
use entities\Import;
use entities\Country;

/**
 * Prepare CRUD methods for working under ORM class \entities\Import
 * @author Anatoly.Bogdanov
 *
 */
class clsAdminImports extends clsAdminEntity
{

    /**
     * Self object
     * @var clsAdminImports $instance
     */
    private static $instance = null;

    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
    private $em;

    /**
     * Singleton
     * @return NULL|\classes\clsAdminImports
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new clsAdminImports();
        }
        return self::$instance;
    }

    /**
     * Constructor of the class.
     * Set entity name, get ORM Entity Manager object
     */
    public function __construct()
    {
        parent::__construct();
        $this->entity = clsAdminCommon::getAdminMessage('import', ADMIN_ENTITIES_BLOCK);
        $this->em = clsDB::getInstance();
    }

    /**
     * Get imports list
     * @param int $page
     * @param int $limit
     * @param string $sort
     * @param string $sorter
     * @param array $filter
     * @return array
     */
    public function getImportsList($page = 1, $limit = DEF_PAGING_NUM, $sort = '', $sorter = 'desc', $filter = array())
    {
        $db = $this->em->createQueryBuilder();
        $db->select('c')->from('entities\Import', 'c');
        $whereClause = $this->getElmFilter($filter, 'entities\Import', 'c');
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
     * Get count of the imports list
     * @param array $filter
     * @return mixed
     */
    public function getImportsListCount($filter = array())
    {
        $whereClause = $this->getElmFilter($filter, 'entities\Import', 'c');
        if (!empty($whereClause)) {
            $whereClause = " WHERE " . $whereClause;
        }
        $query = $this->em->createQuery("SELECT COUNT(c) FROM entities\Import c ".$whereClause);
        return $query->getSingleScalarResult();
    }

    /**
     * Add Import
     * @param string $name
     * name of the Import
     * @param string $img
     * path to file
     * @param string $desc
     * result of import
     * @return boolean
     */
    public function addImport($name, $img = '', $desc = '')
    {
        $res = false;
        if (!empty($name)) {

            $_import = $this->em->getRepository('entities\Import')->findBy(array('name' => $name));
            if ($_import) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $name)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                $import = new Import();
                $import->setName($name)
                       ->setDesc($desc);
                if (!empty($img)) {
                    $import->setPath($img);
                }
                $this->em->persist($import);
                $this->em->flush();
                $res = true;
            }
        }
        return $res;
    }

    /**
     * Get import by ID
     *
     * @param integer $id
     *
     * @return FALSE | \entities\Import
     */
    public function getImportById($id)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if ($id > 0) {
            $res = $this->em->getRepository('entities\Import')->find($id);
        }
        return $res;
    }
}