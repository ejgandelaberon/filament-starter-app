<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SystemRoleEnum;
use OwenIt\Auditing\Auditable as WithAuditing;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole implements Auditable
{
    use WithAuditing;

    public function isSystem(): bool
    {
        return in_array($this->name, SystemRoleEnum::values(), true);
    }
}
