<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * AclMobilePermissionrole
 *
 * @ORM\Table(name="acl_mobile_permissionsroles")
 * @ORM\Entity
 */
class AclMobilePermissionrole extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="role_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected $roleId;

    /**
     * @var integer
     *
     * @ORM\Column(name="perm_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected $permId;


}
