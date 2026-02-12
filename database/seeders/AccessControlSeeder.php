<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SystemRoleEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class AccessControlSeeder extends Seeder
{
    public function run(): void
    {
        $email = str('super-admin')
            ->append(Config::string('app.domain'))
            ->value();

        User::where('email', $email)
            ->sole()
            ->assignRole(
                Role::where('name', SystemRoleEnum::SUPER_ADMIN)->sole()
            );
    }
}
