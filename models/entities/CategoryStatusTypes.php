<?php

namespace entities;

/**
 * Category status types
 */
class CategoryStatusTypes extends AbstractEnum
{
    const STATE_ACTIVE = 'Активна';
    const STATE_DISABLE = 'Неактивна';
    /**
     * @var string $name
     */
    protected static $name = 'category_status_types';
    /**
     * @var array $values
     */
    protected static $values = array(
        1 => self::STATE_ACTIVE,
        2 => self::STATE_DISABLE
    );
}
