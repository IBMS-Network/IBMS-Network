<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users recovery
 *
 * @ORM\Table(name="usersrecovery", uniqueConstraints={@ORM\UniqueConstraint(name="hash", columns={"hash"})})
 * @ORM\Entity
 */
class UsersRecovery extends AbstractEntity
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
     * @ORM\Column(name="email", type="string", length=120, nullable=false)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string", length=32, nullable=false)
     */
    protected $hash;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    protected $status = 0;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return UsersRecovery
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     * @return UsersRecovery
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return UsersRecovery
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

}
