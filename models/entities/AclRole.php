<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * AclRole
 *
 * @ORM\Table(name="acl_roles", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})})
 * @ORM\Entity
 */
class AclRole extends AbstractEntity {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=150, nullable=false)
     */
    protected $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     *
     * @ORM\ManyToMany(targetEntity="AclPermission", inversedBy="roles", cascade={"persist"})
     * @ORM\JoinTable(name="acl_permissionsroles",
     * joinColumns={
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     * },
     * inverseJoinColumns={
     * @ORM\JoinColumn(name="perm_id", referencedColumnName="id")
     * }
     * )
     */
    protected $permissions;

    /**
     * Constructor
     */
    public function __construct(){
        $this->permissions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(){
        return $this->name;
    }

    /**
     * @param $name
     * @return AclRole
     */
    public function setName( $name ){
        $this->name = $name;
        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function getPermissions(){
        return $this->permissions;
    }

    /**
     * @param array $permissions
     * @return AclRole
     */
    public function setPermissions( array $permissions ){
        $this->permissions->clear();
        if( $permissions ){
            foreach( $permissions as $permission ){
                $this->setPermission( $permission );
            }
        }
        return $this;
    }

    /**
     * @param AclPermission $permission
     * @return AlcRole
     */
    public function setPermission( \entities\AclPermission $permission ){
        if( !$this->permissions->contains( $permission ) ){
            $this->permissions->add( $permission );
        }
        return $this;
    }

    /**
     * Method to get permissions list in Array notation
     * @return array
     */
    public function getPermissionsInArray(){
        $permissions = array();
        if( $this->permissions ){
            foreach( $this->permissions->toArray() as $permission ){
                $permissions[$permission->getId()] = $permission->getName();
            }
        }
        return $permissions;
    }

}
