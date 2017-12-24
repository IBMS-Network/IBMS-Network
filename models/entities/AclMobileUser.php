<?php
namespace entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * AclMobileUser
 *
 * @ORM\Table(name="acl_mobile_users", indexes={@ORM\Index(name="fk_mob_users_rid_idx", columns={"mob_role_id"})})
 * @ORM\Entity
 */
class AclMobileUser extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AclMobileRole
     *
     * @ORM\ManyToOne(targetEntity="AclMobileRole")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mob_role_id", referencedColumnName="id")
     * })
     */
    protected $role;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @return int
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @param int $id
     * @return AclMobileUser
     */
    public function setId($id){
        $this->id = (int) $id;
        return $this;
    }

    /**
     * @return \AclMobileRole
     */
    public function getRole(){
        return $this->role;
    }

    /**
     * @param $role
     * @return AclMobileUser
     */
    public function setRole(entities\AclMobileRole $role){
        $this->role = $role;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(){
        return $this->email;
    }

    /**
     * @param $email
     * @return AclMobileUser
     */
    public function setEmail($email){
        $this->email = $email;
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
     * @return AclMobileUser
     */
    public function setPassword($password){
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken(){
       return $this->token;
    }

    /**
     * @param $token
     * @return AclMobileUser
     */
    public function setToken($token){
        $this->token = $token;
        return $this;
    }
}
