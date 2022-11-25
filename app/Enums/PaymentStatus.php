<?php declare(strict_types=0);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static NotPaid()
 * @method static static Paid()
 */
final class PaymentStatus extends Enum
{
    const NotPaid = 0;
    const Paid = 1;
}
