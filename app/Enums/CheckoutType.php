<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OnDelivery()
 * @method static static Momo()
 * @method static static ZaloPay()
 */
final class CheckoutType extends Enum
{
    const OnDelivery = 0;
    const Momo = 1;
    const ZaloPay = 2;
}
