<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 */
class User extends AbstractEntity
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
     * @ORM\Column(name="first_name", type="string", length=150, nullable=false)
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=100, nullable=false)
     */
    protected $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=70, nullable=false)
     */
    protected $password;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=120, nullable=false)
     */
    protected $email;
    
    /**
     * @var string
     *
     * @ORM\Column(name="sex", type="integer", length=6, nullable=true)
     */
    protected $sex;
    
    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    protected $city;
    
    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=true)
     */
    protected $phone;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="reg_date", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    protected $regDate;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birth_date", type="datetime", nullable=true)
     */
    protected $birthDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    protected $status = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=40, nullable=true)
     */
    protected $token;

    
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }
    
    /**
     * @param $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->getFirstName() . ' '. $this->getLastName();
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT, array("cost" => 10));
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param $token
     * @return User
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }
    
    /**
     * @return User
     */
    public function generateToken()
    {
        $this->setToken(substr(password_hash(md5(time()), PASSWORD_BCRYPT, array("cost" => 10)), 0, 40));
        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
    
    public function getRegDate()
    {
        return $this->regDate;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setRegDate(\DateTime $regDate)
    {
        $this->regDate = $regDate;
        return $this;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getSex()
    {
        return $this->sex;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getBirthDate()
    {
        return $this->birthDate;
    }

    public function setSex($sex)
    {
        $this->sex = $sex;
        return $this;
    }

    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    public function setBirthDate(\DateTime $birthDate)
    {
        $this->birthDate = $birthDate;
        return $this;
    }

}
