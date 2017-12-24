<?php

namespace entities;

/**
 * Delivery hours types
 */
class DeliveryHoursTypes extends AbstractEnum
{
    const FIRST_HOURS = 'c 14ч. до 18ч.';
    const SECOND_HOURS = 'c 18ч. до 22ч.';
    /**
     * @var string $name
     */
    protected static $name = 'delivery_hours_types';
    /**
     * @var array $values
     */
    protected static $values = array(
        1 => self::FIRST_HOURS,
        2 => self::SECOND_HOURS,
    );
}
