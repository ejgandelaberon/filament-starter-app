<?php

declare(strict_types=1);

namespace App\Models;

use OwenIt\Auditing\Auditable as WithAuditing;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission implements Auditable
{
    use WithAuditing;
}
