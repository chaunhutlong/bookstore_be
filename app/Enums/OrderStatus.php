<?php declare(strict_types=0);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Ordered()
 * @method static static Processing()
 * @method static static Delivering()
 * @method static static Received()

 */
final class OrderStatus extends Enum
{
    const Ordered = 0;
    const Processing = 1;
    const Delivering = 2;
    const Received = 3;
    const Cancelled = 4;
}
