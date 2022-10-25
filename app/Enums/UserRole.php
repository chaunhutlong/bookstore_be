<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static User()
 * @method static static Seller()
 * @method static static Manager()
 * @method static static Admin()
 */
final class UserRole extends Enum
{
    const User = 1;
    const Seller = 2;
    const Manager = 3;
    const Admin = 4;
}
