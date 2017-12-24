<?php

namespace entities;

/**
 * Users status types
 */
class UserStatusTypes extends AbstractEnum
{
    const STATE_ACTIVE = 'Активен';
    const STATE_BLOCKED = 'Блокирован';
    const STATE_REG = 'Зарегистрирован';
    /**
     * @var string $name
     */
    protected static $name = 'user_statuses_types';
    /**
     * @var array $values
     */
    protected static $values = array(
        1 => self::STATE_ACTIVE,
        2 => self::STATE_BLOCKED,
        3 => self::STATE_REG
    );
}
