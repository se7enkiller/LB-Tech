<?php

namespace Enum;

enum StatusType: int
{
    case DELIVERED = 19;
    case CANCELLED = 11;
    case NEW = 12;
    case HOLD = 15;
    case TRANSIT = 16;
    case ON_DELIVERY = 10;
    case RETURNED = 20;
    case ERROR = 22;
    case PAID_OUT = 9;

    public static function getId($status): int
    {
        return match ($status) {
            'delivered', 'Delivered' => self::DELIVERED->value,
            'cancelled', 'error_from_ds', 'lost', 'Returning' => self::CANCELLED->value,
            'new' => self::NEW->value,
            'send_to_ds',
            'On hold',
            'New',
            'Pending',
            'Assigned to partner',
            'Tracking number received',
            'Shipment created' => self::HOLD->value,
            'on_delivery', 'Return processed', 'Returned to sender' => self::ON_DELIVERY->value,
            'In transit' => self::TRANSIT->value,
            'return' => self::RETURNED->value,
            'paid_out' => self::PAID_OUT->value,
            'Error', 'Out of stock', 'Cancelled', 'Damaged', 'Lost' => self::ERROR->value,
            default => 0,
        };
    }
}
