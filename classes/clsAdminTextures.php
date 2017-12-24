<?php
namespace classes;

use classes\core\clsDB;
use engine\clsAdminEntity;
use engine\modules\admin\clsAdminCommon;
use entities\Texture;
use classes\core\clsCommon;

/**
 * Prepare methods for working under ORM class \entities\Textures
 * @author Anatoly.Bogdanov
 *
 */
class clsAdminTextures extends clsAdminEntity
{

    /**
     * Self object
     * @var clsAdminTextures $instance
     */
    private static $instance = null;

    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
    private $em;

    /**
     * Singleton
     * @return NULL|\classes\clsAdminTextures
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new clsAdminTextures();
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
        $this->entity = clsAdminCommon::getAdminMessage('texture', ADMIN_ENTITIES_BLOCK);
        $this->em = clsDB::getInstance();
    }

    /**
     * Get textures list
     * @param int $page
     * @param int $limit
     * @param string $sort
     * @param string $sorter
     * @return array
     */
    public function getTexturesList($page = 1, $limit = DEF_PAGING_NUM, $sort = '', $sorter = 'desc')
    {
        $db = $this->em->createQueryBuilder();
        $db->select('t')->from('entities\Texture', 't');
        if (!empty($sort) && in_array($sorter, array('asc', 'desc'))) {
            $db->orderBy('t.'.$sort , $sorter);
        }
        $db->setFirstResult(((int)$page - 1) * $limit);
        $db->setMaxResults((int)$limit);

        return $db->getQuery()->getResult();
    }

    /**
     * Get count of the textures list
     * @return mixed
     */
    public function getTexturesListCount()
    {
        $query = $this->em->createQuery("SELECT COUNT(c) FROM entities\Texture t ".$whereClause);
        return $query->getSingleScalarResult();
    }

    /**
     * Get texture by ID
     *
     * @param integer $id
     *
     * @return FALSE | \Doctrine\ORM\EntityRepository
     */
    public function getTextureById($id)
    {
        $res = false;
        $id = clsCommon::isInt($id);
        if ($id > 0) {
            $res = $this->em->getRepository('entities\Texture')->find($id);
        }
        return $res;
    }

    /**
     * Get texture by Name
     *
     * @param string $name
     *
     * @return FALSE | \entities\Texture
     */
    public function getTextureByName($name)
    {
        $res = false;
        if (!empty($name)) {
            $res = $this->em->getRepository('entities\Texture')->findOneBy(array('name' => $name));
        }
        return $res;
    }

    /**
     * Add Texture
     * @param string $name
     * name of the Texture
     * @return FALSE | integer
     */
    public function addTexture($name)
    {
        $res = false;
        if (!empty($name)) {

            $_texture = $this->em->getRepository('entities\Texture')->findBy(array('name' => $name));
            if ($_texture) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_entity_name_exists',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityname}' => $name)
                );
                $this->errors->setError($error, 1, false, true);
            } else {
                $texture = new Texture();
                $texture->setName($name);
                $this->em->persist($texture);
                $this->em->flush();
                $res = $texture->getId();
            }
        }
        return $res;
    }
}