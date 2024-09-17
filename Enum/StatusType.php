<?php

namespace Enum;

enum StatusType: int
{
    case DELIVERED = 19;
    case CANCELLED = 11;
    case NEW = 12;
    case SEND_TO_DS = 15;
    case ON_DELIVERY = 10;
    case RETURNED = 20;
    case PAID_OUT = 9;


    public static function getId($status): int
    {
        return match ($status) {
            'delivered', 'Delivered' => self::DELIVERED,
            'cancelled', 'error_from_ds', 'lost', 'Canceled' => self::CANCELLED,
            'new' => self::NEW,
            'send_to_ds' => self::SEND_TO_DS,
            'on_delivery', 'Out for Delivery' => self::ON_DELIVERY,
            'return' => self::RETURNED,
            'paid_out' => self::PAID_OUT,
            default => 0,
        };
    }
}