<?php

namespace entities;

/**
 * Order payment types
 */
class OrderPaymentTypes extends AbstractEnum
{
    const STATE_CASH = 'Наличные';
    const STATE_ROBOKASSA = 'Робокасса';
    const STATE_YANDEXMONEY = 'Яндекс.Деньги';

    /**
     * @var string $name
     */
    protected static $name = 'order_payment_types';
    /**
     * @var array $values
     */
    protected static $values = array(
        1 => self::STATE_CASH,
        2 => self::STATE_ROBOKASSA,
        3 => self::STATE_YANDEXMONEY,
    );
}
