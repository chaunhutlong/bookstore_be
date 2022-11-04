<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static User()
 * @method static static Manager()
 * @method static static Admin()
 */
final class UserRole extends Enum
{
    const User = 1;
    const Manager = 2;
    const Admin = 3;
}
