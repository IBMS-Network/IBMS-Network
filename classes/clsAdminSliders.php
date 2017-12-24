<?php
namespace classes;

use classes\core\clsCommon;
use classes\core\clsDB;
use engine\clsAdminEntity;
use engine\modules\admin\clsAdminCommon;
use entities\Slider;

/**
 * Prepare CRUD methods for working under ORM class \entities\Slider
 * @author Anatoly.Bogdanov
 *
 */
class clsAdminSliders extends clsAdminEntity
{

    /**
     * Self object
     * @var clsAdminSliders $instance
     */
    private static $instance = null;

    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
    private $em;

    /**
     * Singleton
     * @return NULL|\classes\clsAdminSliders
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new clsAdminSliders();
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
        $this->entity = clsAdminCommon::getAdminMessage('slider', ADMIN_ENTITIES_BLOCK);
        $this->em = clsDB::getInstance();
    }

    /**
     * Get sliders list
     * @param int $page
     * @param int $limit
     * @param string $sort
     * @param string $sorter
     * @param array $filter
     * @return array
     */
    public function getSlidersList($page = 1, $limit = DEF_PAGING_NUM, $sort = '', $sorter = 'desc', $filter = array())
    {
        $db = $this->em->createQueryBuilder();
        $db->select('c')->from('entities\Slider', 'c');
        $whereClause = $this->getElmFilter($filter, 'entities\Slider', 'c');
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
     * Get count of the sliders list
     * @param array $filter
     * @return mixed
     */
    public function getSlidersListCount($filter = array())
    {
        $whereClause = $this->getElmFilter($filter, 'entities\Slider', 'c');
        if (!empty($whereClause)) {
            $whereClause = " WHERE " . $whereClause;
        }
        $query = $this->em->createQuery("SELECT COUNT(c) FROM entities\Slider c ".$whereClause);
        return $query->getSingleScalarResult();
    }

    /**
     * Get slider by ID
     *
     * @param integer $id
     *
     * @return FALSE | \Doctrine\ORM\EntityRepository
     */
    public function getSliderById($id)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if ($id > 0) {
            $res = $this->em->getRepository('entities\Slider')->find($id);
        }
        return $res;
    }

    /**
     * Add Slider
     * @param string $name
     * name of the Slider
     * @param string $desc
     * description
     * @param string $subtitle
     * sub title of slider
     * @param string $phrase
     * phrase
     * @param string $url
     * url
     * @return boolean
     */
    public function addSlider($name, $desc = '', $subtitle = '', $phrase = '', $url = '')
    {
        $res = false;
        if (!empty($name)) {

            $_slider = $this->em->getRepository('entities\Slider')->findBy(array('name' => $name));
            if ($_slider) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $name)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                $slider = new Slider();
                $slider->setName($name)
                    ->setText($desc)
                    ->setSubtitle($subtitle)
                    ->setPhrase($phrase)
                    ->setUrl($url);
                $this->em->persist($slider);
                $this->em->flush();
                $res = true;
            }
        }
        return $res;
    }

    /**
     * Update Slider
     * @param int $id
     * ID of the Slider
     * @param string $name
     * name of the Slider
     * @param string $desc
     * description
     * @param string $subtitle
     * sub title of slider
     * @param string $phrase
     * phrase
     * @param string $url
     * url
     * @return boolean
     */
    public function updateSlider($id, $name, $desc = '', $subtitle = '', $phrase = '', $url = '')
    {
        $res = false;
        if (clsCommon::isInt($id) > 0 && !empty($name)) {
            $db = $this->em->createQueryBuilder();
            $db->select('c')->from('entities\Slider', 'c')
                ->where('c.id != :identifier')->setParameter('identifier', clsCommon::isInt($id))
                ->andWhere('c.name = :name')->setParameter('name', $name);
            $_slider = $db->getQuery()->getResult();
            if ($_slider) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $name)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                $slider = $this->em->getRepository('entities\Slider')->find(clsCommon::isInt($id));
                $slider->setName($name)
                    ->setText($desc)
                    ->setSubtitle($subtitle)
                    ->setPhrase($phrase)
                    ->setUrl($url);
                $this->em->persist($slider);
                $this->em->flush();
                $res = true;
            }
        }
        return $res;
    }

    /**
     * delete Slider
     * @param int $id
     * ID of the Slider
     * @return boolean
     */
    public function deleteSlider($id)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if ( $id > 0) {
            $role = $this->em->getRepository('entities\Slider')->find($id);
            $this->em->remove($role);
            $this->em->flush();
            $res = true;
        }
        return $res;
    }
}