<?php
namespace classes;

use classes\core\clsDB;
use engine\clsAdminEntity;
use engine\modules\admin\clsAdminCommon;
use classes\core\clsCommon;
use entities\Color;

/**
 * Prepare methods for working under ORM class \entities\Color
 * @author Anatoly.Bogdanov
 *
 */
class clsAdminColors extends clsAdminEntity
{

    /**
     * Self object
     * @var clsAdminColors $instance
     */
    private static $instance = null;

    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
    private $em;

    /**
     * Singleton
     * @return NULL|\classes\clsAdminColors
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new clsAdminColors();
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
        $this->entity = clsAdminCommon::getAdminMessage('color', ADMIN_ENTITIES_BLOCK);
        $this->em = clsDB::getInstance();
    }

    /**
     * Get colors list
     * @param int $page
     * @param int $limit
     * @param string $sort
     * @param string $sorter
     * @return array
     */
    public function getColorsList($page = 1, $limit = DEF_PAGING_NUM, $sort = '', $sorter = 'desc')
    {
        $db = $this->em->createQueryBuilder();
        $db->select('c')->from('entities\Color', 'c');
        if (!empty($sort) && in_array($sorter, array('asc', 'desc'))) {
            $db->orderBy('c.'.$sort , $sorter);
        }
        $db->setFirstResult(((int)$page - 1) * $limit);
        $db->setMaxResults((int)$limit);

        return $db->getQuery()->getResult();
    }

    /**
     * Get count of the colors list
     * @return mixed
     */
    public function getColorsListCount()
    {
        $query = $this->em->createQuery("SELECT COUNT(c) FROM entities\Color c ");
        return $query->getSingleScalarResult();
    }

    /**
     * Get color by Name
     *
     * @param string $name
     *
     * @return FALSE | \entities\Color
     */
    public function getColorByName($name)
    {
        $res = false;
        if (!empty($name)) {
            $res = $this->em->getRepository('entities\Color')->findOneBy(array('name' => $name));
        }
        return $res;
    }

    /**
     * Get color by ID
     *
     * @param integer $id
     *
     * @return FALSE | \entities\Color
     */
    public function getColorById($id)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if ($id > 0) {
            $res = $this->em->getRepository('entities\Color')->find($id);
        }
        return $res;
    }

    /**
     * Add Color
     * @param string $name
     * name of the color
     * @return FALSE | \entities\Color
     */
    public function addColor($name)
    {
        $res = false;
        if (!empty($name)) {

            $_color = $this->em->getRepository('entities\Color')->findBy(array('name' => $name));
            if ($_color) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $name)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                $color = new Color();
                $color->setName($name);
                $this->em->persist($color);
                $this->em->flush();
                $res = $color;
            }
        }
        return $res;
    }
}