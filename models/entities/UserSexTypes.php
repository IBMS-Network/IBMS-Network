<?php

namespace entities;

/**
 * Users sex types
 */
class UserSexTypes extends AbstractEnum
{
    const STATE_MALE = 'Мужчина';
    const STATE_FEMALE = 'Женщина';
    /**
     * @var string $name
     */
    protected static $name = 'user_sex_types';
    /**
     * @var array $values
     */
    protected static $values = array(
        1 => self::STATE_MALE,
        2 => self::STATE_FEMALE
    );
}
