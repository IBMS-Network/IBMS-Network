<?php
namespace entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * AclMobilePermission
 *
 * @ORM\Table(name="acl_mobile_permissions", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})})
 * @ORM\Entity
 */
class AclMobilePermission extends AbstractEntity
{
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
     * @ORM\ManyToMany(targetEntity="AclMobileRole", mappedBy="permissions")
     */
    protected $roles;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @param int $id
     * @return AclMobilePermission
     */
    public function setId($id){
        $this->id = (int) $id;
        return $this;
    }

    /**
     * @return string
     */
    public function  getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return AclMobilePermission
     */
    public function setName($name){
        $this->name = $name;
        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function getRoles(){
        return $this->roles;
    }

    public function addRole(\entities\AclMobileRole $role){
        $this->roles[] = $role;
        return $this;
    }
}
