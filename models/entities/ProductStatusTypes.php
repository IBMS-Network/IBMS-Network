<?php

namespace entities;

/**
 * Product status types
 */
class ProductStatusTypes extends AbstractEnum
{
    const STATE_ACTIVE = 'Активен';
    const STATE_DISABLE = 'Диактивирован';
    /**
     * @var string $name
     */
    protected static $name = 'product_status_types';
    /**
     * @var array $values
     */
    protected static $values = array(
        1 => self::STATE_ACTIVE,
        2 => self::STATE_DISABLE
    );
}
