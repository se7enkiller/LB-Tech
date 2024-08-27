<?php

namespace Enum;

class StatusType
{
    const DELIVERED = 19;
    const CANCELLED = 11;
    const NEW = 12;
    const SEND_TO_DS = 15;
    const ERROR_FROM_DS = 11;
    const ON_DELIVERY = 10;
    const RETURNED = 20;
    const LOST = 11;
    const PAID_OUT = 9;

    public static function getId($status): int
    {
        switch ($status) {
            case 'delivered':
                return self::DELIVERED;
            case 'cancelled':
                return self::CANCELLED;
            case 'new':
                return self::NEW;
            case 'send_to_ds':
                return self::SEND_TO_DS;
            case 'error_from_ds':
                return self::ERROR_FROM_DS;
            case 'on_delivery':
                return self::ON_DELIVERY;
            case 'return':
                return self::RETURNED;
            case 'lost':
                return self::LOST;
            case 'paid_out':
                return self::PAID_OUT;
        }

        return 0;
    }
}