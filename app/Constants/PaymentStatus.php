<?php

namespace App\Constants;

class PaymentStatus
{
    public const DUE = 'DUE';
    public const REPAID = 'REPAID';
    public const PARTIAL = 'PARTIAL';

    public const ALL = [
        self::DUE,
        self::REPAID,
        self::PARTIAL,
    ];

}
