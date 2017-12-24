<?php

namespace entities;

/**
 * Product availability types
 */
class ProductAvailabilityTypes extends AbstractEnum
{
    const STATE_OUT = 'Нет в наличии';
    const STATE_IN_STOCK = 'В наличии';
    /**
     * @var string $name
     */
    protected static $name = 'product_availability_types';
    /**
     * @var array $values
     */
    protected static $values = array(
        1 => self::STATE_IN_STOCK,
        2 => self::STATE_OUT,
    );
}
