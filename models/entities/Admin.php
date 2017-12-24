<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Admin
 *
 * @ORM\Table(name="admins", indexes={@ORM\Index(name="role_index_id", columns={"role_id"})})
 * @ORM\Entity
 */
class Admin extends AbstractEntity
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
     * @var \AclRole
     *
     * @ORM\ManyToOne(targetEntity="AclRole")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     * })
     */
    protected $role;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=255, nullable=false)
     */
    protected $login;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    protected $password;
    
    /**
     * @ORM\OneToMany(targetEntity="OrdersComments", mappedBy="admin")
     **/
    protected $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }
    
    /**
     * @return int
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @param int $id
     * @return Admin
     */
    public function setId($id){
        $this->id = (int) $id;
        return $this;
    }

    /**
     * @return \AclRole
     */
    public function getRole(){
        return $this->role;
    }

    /**
     * @param $role
     * @return Admin
     */
    public function setRole(\entities\AclRole $role){
        $this->role = $role;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogin(){
        return $this->login;
    }

    /**
     * @param $login
     * @return Admin
     */
    public function setLogin($login){
        $this->login = $login;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(){
       return $this->password;
    }

    /**
     * @param $password
     * @return Admin
     */
    public function setPassword($password){
        $this->password = $password;
        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }
}
