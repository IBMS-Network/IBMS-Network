<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * AclPermissionrole
 *
 * @ORM\Table(name="acl_permissionsroles")
 * @ORM\Entity
 */
class AclPermissionrole extends AbstractEntity
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
