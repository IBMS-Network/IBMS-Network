<?php
namespace classes;

use engine\clsAdminEntity;

use classes\core\clsCommon;

use engine\modules\admin\clsAdminCommon;

use classes\core\clsDB;

/**
 * Prepare CRUD methods for working under ORM class \entities\Model
 * @author Anatoly.Bogdanov
 *
 */
class clsAdminModels extends clsAdminEntity
{

    /**
     * Self object
     * @var clsAdminModels $instance
     */
    private static $instance = NULL;

    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
    private $em;

    /**
     * Singleton
     * @return NULL|\classes\clsAdminModels
     */
    public static function getInstance()
    {
        if( self::$instance == NULL ){
            self::$instance = new clsAdminModels();
        }
        return self::$instance;
    }

    /**
     * Constructorof the class.
     * Set entity name, get ORM Entity Manager object
     */
    public function __construct()
    {
        parent::__construct();
        $this->entity = clsAdminCommon::getAdminMessage( 'model', ADMIN_ENTITIES_BLOCK );
        $this->em = clsDB::getInstance();
    }

    /**
     * Get countries list
     * @param int $page
     * page number
     * @param int $limit
     * limit elements per page
     * @return array
     */
    public function getModelsList($page = 1, $limit = DEF_PAGING_NUM)
    {
        $db = $this->em->createQueryBuilder();
        $db->select('c')->from('entities\Model', 'c');
        $db->setFirstResult(((int) $page - 1) * $limit);
        $db->setMaxResults((int) $limit);

        return $db->getQuery()->getResult();
    }

    /**
     * Get count of the countries list
     */
    public function getModelsListCount()
    {
        $query = $this->em->createQuery("SELECT COUNT(c) FROM \entities\Model c");
        return $query->getSingleScalarResult();
    }

    /**
     * Get model by ID
     *
     * @param integer $id
     *
     * @return FALSE | \Doctrine\ORM\EntityRepository
     */
    public function getModelById( $id )
    {
        $res = false;
        if( clsCommon::isInt( $id ) > 0 ){
            $res = $this->em->getRepository( 'entities\Model' )->find( clsCommon::isInt( $id ) );
        }
        return $res;
    }

    /**
     * Add Model
     * @param string $name
     * name of the Model
     * @return boolean
     */
    public function addModel( $name )
    {
        $res = false;
        if( !empty( $name ) ){

            $_model = $this->em->getRepository( 'entities\Model' )->findBy( array( 'name' => $name ) );
            if( $_model ){
                $error = clsAdminCommon::getAdminMessage( 'error_entity_name_exists', ADMIN_ERROR_BLOCK, array( '{%entity}' => $this->entity, '{%entityname}' => $name ) );
                $this->errors->setError( $error, 1, false, true );
            }else{
                $model = new \entities\Model();
                $model->setName( $name );
                $this->em->persist( $model );
                $this->em->flush();
                $res = true;
            }
        }
        return $res;
    }

    /**
     * Update Model
     * @param int $id
     * identificator of the Model
     * @param string $name
     * name of the Model
     * @return boolean
     */
    public function updateModel( $id, $name )
    {
        $res = false;
        if( clsCommon::isInt( $id ) > 0 && !empty( $name ) ){
            $db = $this->em->createQueryBuilder();
            $db->select('c')->from('entities\Model', 'c')
            ->where('c.id != :identifier')->setParameter('identifier', clsCommon::isInt($id))
            ->andWhere('c.name = :name')->setParameter('name', $name);
            $_model = $db->getQuery()->getResult();
            if( $_model ){
                $error = clsAdminCommon::getAdminMessage( 'error_entity_name_exists', ADMIN_ERROR_BLOCK, array( '{%entity}' => $this->entity, '{%entityname}' => $name ) );
                $this->errors->setError( $error, 1, false, true );
            }else{
                $model = $this->em->getRepository( 'entities\Model' )->find( clsCommon::isInt( $id ) );
                $model->setName( $name );
                $this->em->persist( $model );
                $this->em->flush();
                $res = true;
            }
        }
        return $res;
    }

    /**
     * delete Model
     * @param int $id
     * identificator of the Model
     * @return boolean
     */
    public function deleteModel( $id )
    {
        $res = false;
        if( clsCommon::isInt( $id ) > 0 ){
            $role = $this->em->getRepository( 'entities\Model' )->find( clsCommon::isInt( $id ) );
            $this->em->remove( $role );
            $this->em->flush();
            $res = true;
        }
        return $res;
    }
}