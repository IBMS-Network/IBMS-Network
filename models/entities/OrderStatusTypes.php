<?php

namespace entities;

/**
 * Order status types
 */
class OrderStatusTypes extends AbstractEnum
{
    const STATE_REJECTED = 'Отклонен';
    const STATE_WAITING = 'В ожидании';
    const STATE_PAYOFF = 'Оплачен';
    const STATE_DELIVERY = 'Доставка';
    const STATE_FINISHED = 'Завершен';
    const STATE_RESERVED = 'Зарезервирован';
    const STATE_CLOSED = 'Закрыт';
    /**
     * @var string $name
     */
    protected static $name = 'order_status_types';
    /**
     * @var array $values
     */
    protected static $values = array(
        0 => self::STATE_REJECTED,
        1 => self::STATE_WAITING,
        2 => self::STATE_PAYOFF,
        3 => self::STATE_DELIVERY,
        4 => self::STATE_FINISHED,
        5 => self::STATE_RESERVED,
        6 => self::STATE_CLOSED
    );
}
