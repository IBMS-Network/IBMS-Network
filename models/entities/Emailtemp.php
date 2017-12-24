<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Email templates
 *
 * @ORM\Table(name="email_templates", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})})
 * @ORM\Entity
 */
class Emailtemp extends AbstractEntity {

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
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=150, nullable=false)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=150, nullable=false)
     */
    protected $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=400, nullable=false)
     */
    protected $value;

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
     * @param string $name
     * @return Emailtemp
     */
    public function setName( $name ){
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(){
        return $this->email;
    }

    /**
     * @param string $email
     * @return Emailtemp
     */
    public function setEmail( $email ){
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject(){
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return Emailtemp
     */
    public function setSubject( $subject ){
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(){
        return $this->value;
    }

    /**
     * @param string $value
     * @return Emailtemp
     */
    public function setValue( $value ){
        $this->value = $value;
        return $this;
    }

}
